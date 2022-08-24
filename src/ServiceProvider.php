<?php

namespace Actengage\Deployer;

use Actengage\Deployer\Console\Commands\Deploy;
use Actengage\Deployer\Console\Commands\ListBundles;
use Actengage\Deployer\Console\Commands\Prune;
use Actengage\Deployer\Console\Commands\Rollback;
use Actengage\Deployer\Console\Commands\Status;
use Actengage\Deployer\Contracts\LoggerRepository as LoggerRepositoryInterface;
use Actengage\Deployer\Contracts\PathProvider as PathProviderInterface;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        $this->publishes([
            __DIR__.'/../config/deployer.php' => config_path('deployer.php'),
        ], 'deployer-config');
        $this->mergeConfigFrom(
            __DIR__.'/../config/deployer.php', 'deployer'
        );

        if ($this->app->runningInConsole()) {
            $this->commands([
                Deploy::class,
                Rollback::class,
                ListBundles::class,
                Status::class,
                Prune::class,
            ]);
        }

        $this->app->singleton(FilesystemUtility::class);
        $this->app->singleton(BundlesAccessor::class);
        $this->app->singleton(CurrentBundleManager::class);
        $this->app->singleton(ArtifactDeployer::class);
        $this->app->singleton(BundleDeployer::class);
        $this->app->singleton(BundlePruner::class);
        $this->app->singleton(PathProviderInterface::class, PathProvider::class);
        $this->app->singleton(LoggerRepositoryInterface::class, LoggerRepository::class);

        $this->app->when(PathProvider::class)
            ->needs('$deploymentDir')
            ->give(getcwd());

        $this->app->when(BundleDeployer::class)
            ->needs('$artifactRules')
            ->giveConfig('deployer.artifacts');
    }

    public function boot()
    {
    }
}
