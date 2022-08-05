<?php

namespace Actengage\Deployer\Console\Commands;

use Actengage\Deployer\BundleDeployer;
use Actengage\Deployer\BundleExtractor;
use Actengage\Deployer\CommandLogger;
use Actengage\Deployer\DeployerException;
use Illuminate\Console\Command;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * A command that gets pre-built artifacts.
 *
 * A custom Artisan command that gets, extracts, and deploys the artifacts in a given bundle.
 */
final class GetArtifacts extends Command
{
    protected $signature = 'artifacts:get {bundle} {--verbosity=1}';

    protected $description = 'Safely extracts and deploys artifacts from the given bundle.';

    public function handle(BundleExtractor $extractor, BundleDeployer $deployer): int
    {
        $logger = $this->createLogger();

        $extractedPath = $extractor->extract($this->argument('bundle'), $logger);
        $deployer->deploy($extractedPath, $logger);

        return 0;
    }

    private function createLogger(): LoggerInterface
    {
        $logLevel = match ((int) $this->option('verbosity')) {
            0 => LogLevel::ERROR,
            1 => LogLevel::NOTICE,
            2 => LogLevel::INFO,
            default => LogLevel::DEBUG,
        };
        return new CommandLogger($this, $logLevel);
    }
}
