<?php

namespace Actengage\Deployer\Console\Commands;

use Actengage\Deployer\Bundle;
use Actengage\Deployer\BundleDeployer;
use Actengage\Deployer\Contracts\BundlesRepository;
use Actengage\Deployer\Contracts\LoggerRepository;

/**
 * A command that gets pre-built artifacts.
 *
 * A custom Artisan command that gets, extracts, and deploys the artifacts in a given bundle.
 */
final class Deploy extends Command
{
    protected $signature = 'deployer {--latest} {--commit=none} {--version=none} {--verbosity=1}';

    protected $description = 'Safely deploys artifacts from the given bundle.';

    public function handle(LoggerRepository $logger, BundlesRepository $bundles, BundleDeployer $deployer): int
    {
        $logger->set($this->createLogger());

        $bundle = $this->getBundle($bundles);

        if (! $bundle) {
            $this->error('The bundle to deploy must be given with one of the following options: --latest, --commmit=, or --version.');
        }

        $deployer->deploy($this->argument('bundle'));

        return 0;
    }

    private function getBundle(BundlesRepository $bundles): ?Bundle
    {
        if ($this->option('latest')) {
            return $bundles->all(limit: 1)->first();
        } else if ($this->option('version') !== 'none') {
            return $bundles->whereVersion($this->option('version'), limit: 1)->first();
        } else if ($this->option('commit') !== 'none') {
            return $bundles->whereCommit($this->option('commit'), limit: 1)->first();
        } else {
            return null;
        }
    }
}
