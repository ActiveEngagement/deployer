<?php

namespace Actengage\Deployer\Console\Commands;

use Actengage\Deployer\Bundle;
use Actengage\Deployer\Contracts\BundlesRepository;
use Actengage\Deployer\Contracts\LoggerRepository;

/**
 * A command that lists bundles
 *
 * A custom Artisan command that traverses the bundles directory for available, deployable artifact bundles and displays
 * them to the user.
 */
final class ListBundles extends Command
{
    protected $signature = 'deployer:list {--limit=10} {--verbosity=1}';

    protected $description = 'Lists all deployable artifact bundles.';

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
                $n, // #
                $bundle->bundled_at->format('Y-m-d H:i'), // Bundled At
                $bundle->version, // Version
                $bundle->shortCommit(), // Commit
            ];
            $n++;
        });

        $this->table($headers, $rows);

        return 0;
    }
}
