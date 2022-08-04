<?php

namespace Actengage\Deployer;

use Actengage\Deployer\Console\Commands\GetArtifacts;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/deployer.php', 'deployer'
        );

        if ($this->app->runningInConsole()) {
            $this->commands([
                GetArtifacts::class
            ]);
        }

        $this->app->singleton(FilesystemUtility::class);
        $this->app->singleton(ArtifactDeployer::class);
        $this->app->singleton(BundleDeployer::class);
        $this->app->singleton(BundleExtractor::class);

        $this->app->when(BundleExtractor::class)
            ->needs('$bundlesDir')
            ->giveConfig('deployer.bundles_dir');

        $this->app->when(BundleExtractor::class)
            ->needs('$extractionDir')
            ->giveConfig('deployer.extraction_dir');

        $this->app->when(ArtifactDeployer::class)
            ->needs('$backupDir')
            ->giveConfig('deployer.backup_dir');

        $this->app->when(BundleDeployer::class)
            ->needs('$artifactRules')
            ->giveConfig('deployer.artifacts');
    }

    public function boot()
    {

    }
}