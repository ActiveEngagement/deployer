<?php

namespace Actengage\Deployer\Console\Commands;

use Actengage\Deployer\Bundle;
use Actengage\Deployer\CommandLogger;
use Actengage\Deployer\Contracts\BundlesRepository;
use Actengage\Deployer\Contracts\LoggerRepository;
use Illuminate\Console\Command;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

final class BundlesList extends Command
{
    protected $signature = 'deployer:list {--limit=10} {--verbosity=1}';

    protected $description = '';

    public function handle(LoggerRepository $logger, BundlesRepository $bundles): int
    {
        $logger->set($this->createLogger());

        $headers = ['#', 'Bundled At', 'Version', 'Commit'];
        $rows = [];

        $all = $bundles->all(limit: (int) $this->option('limit'));

        if ($all->isEmpty()) {
            $this->info('No bundles found!');
            return 0;
        }
        
        $n = 1;

        $all->each(function (Bundle $bundle) use (&$n, &$rows) {
            $rows[] = [
                $n,
                $bundle->bundled_at->format('Y-m-d H:i'),
                $bundle->version,
                $bundle->shortCommit()
            ];
            $n++;
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
