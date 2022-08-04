<?php

namespace Tests;

use Actengage\Deployer\ArtifactDeployer;
use Actengage\Deployer\BundleDeployer;
use Actengage\Deployer\BundleExtractor;
use Actengage\Deployer\Contracts\PathProvider as PathProviderInterface;
use Actengage\Deployer\FilesystemUtility;
use Actengage\Deployer\PathProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use ReflectionClass;

class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->registerBindings();
        $this->setUpFilesystem();
        chdir($this->testsDir().'storage/app');
    }

    protected function testsDir(): string
    {
        $reflector = new ReflectionClass(self::class);
        $path = $reflector->getFileName();

        return dirname($path).'/';
    }

    private function registerBindings(): void
    {
        $this->app->singleton(LoggerInterface::class, NullLogger::class);
        $this->app->singleton(FilesystemUtility::class);
        $this->app->singleton(ArtifactDeployer::class);
        $this->app->singleton(BundleDeployer::class);
        $this->app->singleton(BundleExtractor::class);
        $this->app->singleton(PathProviderInterface::class, PathProvider::class);

        $this->app->when(PathProvider::class)
            ->needs('$bundlesDir')
            ->give($this->testsDir().'storage/bundles/deeply/nested');

        $this->app->when(PathProvider::class)
            ->needs('$extractionDir')
            ->give($this->testsDir().'storage/path/to/extraction/dir');

        $this->app->when(PathProvider::class)
            ->needs('$backupDir')
            ->give($this->testsDir().'storage/backups');

        $this->app->when(PathProvider::class)
            ->needs('$deploymentDir')
            ->give($this->testsDir().'storage/app');

        $this->app->when(BundleDeployer::class)
            ->needs('$artifactRules')
            ->give([
                'artifact1' => 'public/build',
                'artifact2' => 'public/build2',
                'artifact3.txt' => 'unrelated/built_file.txt',
            ]);
    }

    /**
     * The filesystem under `storage/` will be set up like:
     * storage
     *   bundles
     *     deeply
     *       nested
     *         an_example_bundle.tar.gz
     *   path
     *     to
     *       extraction
     *         dir
     *   backups
     *   app
     *     public
     *       build
     *         should
     *         be.txt
     *         backed_up
     *     unrelated
     *       do
     *         not
     *           touch
     *     random.txt
     */
    private function setUpFilesystem(): void
    {
        $filesystem = $this->app->make(FilesystemUtility::class);
        $testsDir = $this->testsDir();
        $storageDir = $testsDir.'storage/';

        if (file_exists($storageDir)) {
            $filesystem->delete($storageDir);
        }

        $bundlesDir = $storageDir.'bundles/deeply/nested/';
        mkdir($bundlesDir, recursive: true);

        $extractionDir = $storageDir.'path/to/extraction/dir/';
        mkdir($extractionDir, recursive: true);

        $backupsDir = $storageDir.'backups/';
        mkdir($backupsDir, recursive: true);

        $appDir = $storageDir.'app/';
        $publicDir = $appDir.'public/';
        $initialBuildDir = $publicDir.'build/';
        mkdir($initialBuildDir, recursive: true);
        mkdir($initialBuildDir.'should', recursive: true);
        mkdir($initialBuildDir.'backed_up', recursive: true);
        file_put_contents($initialBuildDir.'be.txt', 'Hello!');
        $unrelatedDir = $appDir.'unrelated/do/not/touch/';
        $unrelatedPublicDir = $publicDir.'unrelated/stay/away/';
        mkdir($unrelatedDir, recursive: true);
        mkdir($unrelatedPublicDir, recursive: true);

        copy($testsDir.'bundles/an_example_bundle.tar.gz', $bundlesDir.'an_example_bundle.tar.gz');
        copy($testsDir.'bundles/bundle_two.tar.gz', $bundlesDir.'bundle_two.tar.gz');
        file_put_contents($appDir.'random.txt', 'Some random data.');
    }
}
