<?php

namespace Actengage\Deployer\Console\Commands;

use Actengage\Deployer\Bundle;
use Actengage\Deployer\BundleDeployer;
use Actengage\Deployer\BundlesAccessor;
use Actengage\Deployer\Contracts\BundlesRepository;
use Actengage\Deployer\Contracts\LoggerRepository;
use Illuminate\Support\Str;

/**
 * A command that gets pre-built artifacts.
 *
 * A custom Artisan command that gets, extracts, and deploys the artifacts in a given bundle.
 */
final class Deploy extends Command
{
    protected $signature = 'deployer
                            {--L|latest}
                            {--C|current}
                            {--c|commit=none}
                            {--r|bundle-version=none}
                            {--N|number=none}
                            {--verbosity=1}';

    protected $description = 'Safely deploys artifacts from the given bundle.';

    public function handle(LoggerRepository $logger, BundlesAccessor $bundles, BundleDeployer $deployer): int
    {
        $logger->set($this->createLogger());

        $bundle = $this->getBundle($bundles);

        if (! $bundle) {
            return 1;
        }

        $deployer->deploy($bundle->path);

        return 0;
    }

    private function getBundle(BundlesAccessor $bundles): ?Bundle
    {
        $bundle = null;

        $number = $this->option('number');
        $version = $this->option('bundle-version');
        $commit = $this->option('commit');

        if ($this->option('latest')) {
            $bundle = $bundles->all(limit: 1)->first();

            if (is_null($bundle)) {
                $this->error('No latest bundle found.');
            }
        } else if ($this->option('current')) {
            $bundle = $bundles->current();

            if (is_null($bundle)) {
                $this->warnHeadBroken();
                $this->error('No current bundle found.');
            }
        } else if ($number !== 'none') {
            $bundle = $bundles->all()->get($number);

            if (is_null($bundle)) {
                $this->error("No bundle with number $number found.");
            }
        } else if ($version !== 'none') {
            $bundle = $bundles->all()->first(fn ($b) => $b->version === $version);

            if (is_null($bundle)) {
                $this->error("No bundle with version $version found.");
            }
        } else if ($commit !== 'none') {
            $matches = $bundles->all()->filter(fn ($b) => Str::startsWith($b->commit, $commit));

            if ($matches->count() === 1) {
                $bundle = $matches->first();
            } else if ($matches->count() === 0) {
                $this->error("No bundles with commit $commit found.");
            } else {
                $this->error('Ambiguous commit SHA!');
            }
        } else {
            $this->error('The bundle to deploy must be given with one of the following options: --latest, --commit, or --bundle-version.');
        }

        return $bundle;
    }
}
