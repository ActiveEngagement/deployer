<?php

namespace Actengage\Deployer\Console\Commands;

use Actengage\Deployer\BundlePruner;
use Actengage\Deployer\Console\Commands\Command;
use Actengage\Deployer\Contracts\LoggerRepository;

/**
 * A command that prunes old bundles.
 *
 * A custom Artisan command that removes any old bundles in the bundles directory.
 */
final class Prune extends Command
{
    protected $signature = 'deployer:prune {--keep=5} {--verbosity=1}';

    protected $description = 'Prunes old bundles from the bundles directory.';

    public function handle(LoggerRepository $logger, BundlePruner $pruner): int
    {
        $logger->set($this->createLogger());

        $deleted = $pruner->prune((int) $this->option('keep'));

        if ($deleted > 0) {
            $this->info("Removed $deleted bundles.");
        } else {
            $this->info('No bundles to remove.');
        }

        return 0;
    }
}