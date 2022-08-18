<?php

namespace Actengage\Deployer\Console\Commands;

use Actengage\Deployer\BundlePruner;
use Actengage\Deployer\Contracts\AnsiFilter;
use Actengage\Deployer\Contracts\LoggerRepository;

/**
 * A command that prunes old bundles.
 *
 * A custom Artisan command that removes any old bundles in the bundles directory.
 */
final class Prune extends Command
{
    protected $signature = 'deployer:prune
                            {--keep=5 : The number of most recent bundles to keep.}
                            {--include-invalid : Specifies that bundles with missing/malformed manifests should be removed.}';

    protected $description = 'Prunes old bundles from the bundles directory.';

    public function handle(BundlePruner $pruner): int {
        $this->setup();

        $deleted = $pruner->prune((int) $this->option('keep'), $this->option('include-invalid'));

        if ($deleted > 0) {
            $this->info("Removed $deleted bundles.");
        } else {
            $this->info('No bundles to remove.');
        }

        return 0;
    }
}
