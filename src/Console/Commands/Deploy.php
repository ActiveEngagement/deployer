<?php

namespace Actengage\Deployer\Console\Commands;

use Actengage\Deployer\Bundle;
use Actengage\Deployer\BundleDeployer;
use Actengage\Deployer\BundlesAccessor;
use Actengage\Deployer\Contracts\LoggerRepository;
use Actengage\Deployer\CurrentBundleManager;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * A command that deploys artifact bundles.
 *
 * A custom Artisan command that deploys the artifacts in an artifact bundle to the application.
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

    protected $description = 'Deploys an artifact bundle.';

    public function handle(
        LoggerRepository $logger,
        BundlesAccessor $bundles,
        CurrentBundleManager $currentBundle,
        BundleDeployer $deployer
    ): int {
        $logger->set($this->createLogger());

        $bundle = $this->getBundle($bundles->all(), $currentBundle);

        if (! $bundle) {
            return 1;
        }

        $deployer->deploy($bundle->path);

        return 0;
    }

    private function getBundle(Collection $bundles, CurrentBundleManager $currentBundle): ?Bundle
    {
        $bundle = null;

        $number = $this->option('number');
        $version = $this->option('bundle-version');
        $commit = $this->option('commit');

        if ($this->option('latest')) {
            $bundle = $bundles->first();

            if (is_null($bundle)) {
                $this->error('No latest bundle found.');
            }
        } elseif ($this->option('current')) {
            $bundle = $bundles->first(fn ($b) => $currentBundle->is($b));

            if (is_null($bundle)) {
                $this->warnHeadBroken();
                $this->error('No current bundle found.');
            }
        } elseif ($number !== 'none') {
            $bundle = $bundles->get($number);

            if (is_null($bundle)) {
                $this->error("No bundle with number $number found.");
            }
        } elseif ($version !== 'none') {
            $bundle = $bundles->first(fn ($b) => $b->version === $version);

            if (is_null($bundle)) {
                $this->error("No bundle with version $version found.");
            }
        } elseif ($commit !== 'none') {
            $matches = $bundles->filter(fn ($b) => Str::startsWith($b->commit, $commit));

            if ($matches->count() === 1) {
                $bundle = $matches->first();
            } elseif ($matches->count() === 0) {
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
