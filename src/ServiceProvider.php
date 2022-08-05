<?php

namespace Actengage\Deployer;

use Actengage\Deployer\Console\Commands\Artifacts;
use Actengage\Deployer\Console\Commands\Install;
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
                Artifacts::class,
                Install::class,
            ]);
        }

        $this->app->singleton(FilesystemUtility::class);
        $this->app->singleton(ArtifactDeployer::class);
        $this->app->singleton(BundleDeployer::class);
        $this->app->singleton(BundleExtractor::class);
        $this->app->singleton(PathProviderInterface::class, PathProvider::class);

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
