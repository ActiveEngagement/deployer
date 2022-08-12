<?php

namespace Actengage\Deployer\Console\Commands;

use Actengage\Deployer\Bundle;
use Actengage\Deployer\BundleDeployer;
use Actengage\Deployer\BundlesAccessor;
use Actengage\Deployer\Contracts\BundlesRepository;
use Actengage\Deployer\Contracts\LoggerRepository;
use Actengage\Deployer\CurrentBundleManager;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * A command that rolls back a deployment.
 *
 * A custom Artisan command that deploys an artifact bundle that is a number of steps before the current one.
 * 
 * The effect is something like "rolling back" a deployment, since a previous bundle is deployed.
 */
final class Rollback extends Command
{
    protected $signature = 'deployer:rollback
                            {step=1}
                            {--verbosity=1}';

    protected $description = 'Deploys an artifact bundle a number of steps before the current one.';

    public function handle
    (
        LoggerRepository $logger,
        BundlesAccessor $bundles,
        CurrentBundleManager $currentBundle,
        BundleDeployer $deployer
    ): int
    {
        $logger->set($this->createLogger());

        $all = $bundles->all();
        $currentNumber = $all->search(fn ($b) => $currentBundle->is($b));
        $newNumber = $currentNumber + $this->argument('step');
        $bundle = $all->get($newNumber);

        if (! $bundle) {
            $this->error("We can't go back that far!");
            return 1;
        }

        $deployer->deploy($bundle->path);

        return 0;
    }
}
