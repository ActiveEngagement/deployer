<?php

namespace Actengage\Deployer\Console\Commands;

use Actengage\Deployer\BundleDeployer;
use Actengage\Deployer\Contracts\LoggerRepository;

/**
 * A command that gets pre-built artifacts.
 *
 * A custom Artisan command that gets, extracts, and deploys the artifacts in a given bundle.
 */
final class Artifacts extends Command
{
    protected $signature = 'deployer:artifacts {bundle} {--verbosity=1}';

    protected $description = 'Safely deploys artifacts from the given bundle.';

    public function handle(LoggerRepository $logger, BundleDeployer $deployer): int
    {
        $logger->set($this->createLogger());
        $deployer->deploy($this->argument('bundle'));

        return 0;
    }
}
