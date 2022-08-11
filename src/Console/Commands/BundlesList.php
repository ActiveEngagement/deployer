<?php

namespace Actengage\Deployer\Console\Commands;

use Actengage\Deployer\Bundle;
use Actengage\Deployer\CommandLogger;
use Actengage\Deployer\Contracts\BundlesRepository;
use Illuminate\Console\Command;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

final class BundlesList extends Command
{
    protected $signature = 'deployer:list {--limit=10} {--verbosity=1}';

    protected $description = '';

    public function handle(BundlesRepository $bundles): int
    {
        $logger = $this->createLogger();

        $headers = ['Bundled At', 'Version', 'Commit'];
        $rows = [];

        $bundles->all(limit: (int) $this->option('limit'), logger: $logger)->each(function (Bundle $bundle) use ($rows) {
            $rows [] = [
                $bundle->bundled_at->toDateTimeString(),
                $bundle->version,
                $bundle->commit
            ];
        });

        $this->table($headers, $rows);

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
