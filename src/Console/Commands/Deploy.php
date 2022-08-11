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
    protected $signature = 'deployer {--latest} {--commit=none} {--release=none} {--verbosity=1}';

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
        if ($this->option('latest')) {
            $bundle = $bundles->all(limit: 1)->first();

            if (is_null($bundle)) {
                $this->error('No latest bundle found.');
            }
        } else if ($this->option('release') !== 'none') {
            $version = $this->option('release');
            $bundle = $bundles->all()->first(fn (Bundle $b) => $b->release === $this->option('release'));

            if (is_null($bundle)) {
                $this->error("No bundle with version $version found.");
            }
        } else if ($this->option('commit') !== 'none') {
            $commit = $this->option('commit');
            $matches = $bundles->all()->filter(fn (Bundle $b) => Str::startsWith($b->commit, $commit));

            if ($matches->count() === 1) {
                $bundle = $matches->first();
            } else if ($matches->count() === 0) {
                $this->error("No bundles with commit $commit found.");
            } else {
                $this->error('Ambiguous commit SHA!');
            }
        } else {
            $this->error('The bundle to deploy must be given with one of the following options: --latest, --commit, or --release.');
        }

        return $bundle;
    }
}
