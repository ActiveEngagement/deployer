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
    protected $signature = 'deployer {--latest} {--commit=none} {--release=none} {--verbosity=1}';

    protected $description = 'Safely deploys artifacts from the given bundle.';

    public function handle(LoggerRepository $logger, BundlesRepository $bundles, BundleDeployer $deployer): int
    {
        $logger->set($this->createLogger());

        $bundle = $this->getBundle($bundles);

        if (! $bundle) {
            return 1;
        }

        $deployer->deploy($bundle->path);

        return 0;
    }

    private function getBundle(BundlesRepository $bundles): ?Bundle
    {
        if ($this->option('latest')) {
            return $bundles->all(limit: 1)->first();
        } else if ($this->option('release') !== 'none') {
            return $bundles->whereVersion($this->option('release'), limit: 1)->first();
        } else if ($this->option('commit') !== 'none') {
            $matches = $bundles->whereCommit($this->option('commit'));

            if ($matches->count() > 1) {
                $this->error('Ambiguous commit SHA!');
                return null;
            } else {
                return $matches->first();
            }
        } else {
            $this->error('The bundle to deploy must be given with one of the following options: --latest, --commit, or --release.');
            return null;
        }
    }
}
