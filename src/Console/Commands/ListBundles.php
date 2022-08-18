<?php

namespace Actengage\Deployer\Console\Commands;

use Actengage\Deployer\AnsiUtility;
use Actengage\Deployer\Bundle;
use Actengage\Deployer\BundlesAccessor;
use Actengage\Deployer\Contracts\LoggerRepository;
use Actengage\Deployer\CurrentBundleManager;

/**
 * A command that lists bundles.
 *
 * A custom Artisan command that traverses the bundles directory for available, deployable artifact bundles and displays
 * them to the user.
 */
final class ListBundles extends Command
{
    protected $signature = 'deployer:list {--limit=10}';

    protected $description = 'Lists all deployable artifact bundles.';

    public function handle(
        LoggerRepository $logger,
        BundlesAccessor $bundles,
        CurrentBundleManager $currentBundle,
        AnsiUtility $ansi
    ): int {
        $logger->set($this->createLogger());

        $headers = ['#', 'Bundled At', 'Initiated By', 'Version', 'Commit'];
        $rows = [];

        $all = $bundles->all()->take((int) $this->option('limit'));

        if ($all->isEmpty()) {
            $this->info('No bundles found!');

            return 0;
        }

        $currentBundleNumber = $all->search(fn ($b) => $currentBundle->is($b));

        if ($currentBundleNumber === false) {
            $this->warnHeadBroken();
        }

        $all->each(function (Bundle $bundle, int $n) use (&$rows, $currentBundleNumber, $ansi) {
            $current = $n === $currentBundleNumber;

            $rows[] = $this->row([
                $n,
                $bundle->bundled_at->format('Y-m-d H:i'),
                $bundle->initiator,
                $bundle->version,
                $bundle->shortCommit(),
            ], $current, $ansi);
        });

        $this->table($headers, $rows);

        return 0;
    }

    private function row(array $columns, bool $current, AnsiUtility $ansi): array
    {
        return array_map(fn ($c) => $current ? $ansi->bold($c) : $c, $columns);
    }
}
