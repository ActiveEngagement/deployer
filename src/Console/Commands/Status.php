<?php

namespace Actengage\Deployer\Console\Commands;

use Actengage\Deployer\AnsiColor;
use Actengage\Deployer\AnsiUtility;
use Actengage\Deployer\Bundle;
use Actengage\Deployer\BundlesAccessor;
use Actengage\Deployer\Contracts\LoggerRepository;
use Actengage\Deployer\CurrentBundleManager;

/**
 * A command that shows status
 *
 * A custom Artisan command that displays information about the currently deployed bundle to the user.
 */
final class Status extends Command
{
    protected $signature = 'deployer:status';

    protected $description = 'Displays information about the current deployment.';

    public function handle(
        LoggerRepository $logger,
        BundlesAccessor $bundles,
        CurrentBundleManager $currentBundle,
        AnsiUtility $ansi
    ): int {
        $logger->set($this->createLogger());

        $all = $bundles->all();
        $currentNumber = $all->search(fn ($b) => $currentBundle->is($b));
        $bundle = $all->get($currentNumber);

        if ($currentNumber === false) {
            $this->errorHeadBroken();

            return 1;
        }

        if ($bundle->commit) {
            $this->line('Commit '.$ansi->bold($bundle->shortCommit()));
        }

        if ($bundle->version) {
            $this->line('Version '.$ansi->bold($bundle->version));
        }

        $initiator = $ansi->bold($bundle->initiator ?? $ansi->colored('unknown author', AnsiColor::YELLOW));
        $this->line("Created by $initiator");

        $timestamp = $bundle->bundled_at->format('Y-m-d h:i A');
        $this->line('Bundled '.$timestamp);

        $this->newLine();

        if ($currentNumber === 0) {
            $this->line('You are '.$ansi->colored('up to date', AnsiColor::GREEN).' with the latest deployment.');
        } elseif ($currentNumber === 1) {
            $this->warn('You are 1 deployment behind.');
        } else {
            $this->warn("You are $currentNumber deployments behind.");
        }

        return 0;
    }
}
