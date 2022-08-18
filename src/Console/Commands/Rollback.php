<?php

namespace Actengage\Deployer\Console\Commands;

use Actengage\Deployer\Bundle;
use Actengage\Deployer\BundleDeployer;
use Actengage\Deployer\BundlesAccessor;
use Actengage\Deployer\Contracts\LoggerRepository;
use Actengage\Deployer\CurrentBundleManager;

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
                            {step=1 : How many deployments to roll back.}';

    protected $description = 'Deploys an artifact bundle a number of steps before the current one.';

    public function handle(
        LoggerRepository $logger,
        BundlesAccessor $bundles,
        CurrentBundleManager $currentBundle,
        BundleDeployer $deployer
    ): int {
        $logger->set($this->createLogger());

        $all = $bundles->all();
        $currentNumber = $all->search(fn ($b) => $currentBundle->is($b));

        if ($currentNumber === false) {
            $this->errorHeadBroken();

            return 1;
        }

        $newNumber = $currentNumber + $this->argument('step');
        $bundle = $all->get($newNumber);

        if (! $bundle) {
            $this->error("We can't go back that far!");

            return 1;
        }

        $deployer->deploy($bundle->path);

        $this->info('Bundle deployed!');
        $this->newLine();

        $this->call('deployer:status');

        return 0;
    }
}
