<?php

namespace Tests;

use Actengage\Deployer\ArtifactDeployer;
use Actengage\Deployer\BundleDeployer;
use Actengage\Deployer\BundlePruner;
use Actengage\Deployer\Contracts\PathProvider as PathProviderInterface;
use Actengage\Deployer\FilesystemUtility;
use Orchestra\Testbench\TestCase as BaseTestCase;
use ReflectionClass;
use Tests\Support\PathProvider;
use Actengage\Deployer\LoggerRepository;
use Actengage\Deployer\Contracts\LoggerRepository as LoggerRepositoryInterface;

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
        $this->app->singleton(FilesystemUtility::class);
        $this->app->singleton(ArtifactDeployer::class);
        $this->app->singleton(BundleDeployer::class);
        $this->app->singleton(BundlePruner::class);
        $this->app->singleton(PathProviderInterface::class, PathProvider::class);
        $this->app->singleton(LoggerRepositoryInterface::class, LoggerRepository::class);

        $this->app->when(PathProvider::class)
            ->needs('$testsDir')
            ->give($this->testsDir());

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
     *         an_example_bundle
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
        $filesystem = $this->app->get(FilesystemUtility::class);
        $testsDir = $this->testsDir();
        $storageDir = $testsDir.'storage/';

        if (file_exists($storageDir)) {
            $filesystem->delete($storageDir);
        }

        $bundlesDir = $storageDir.'bundles/deeply/nested/';
        mkdir($bundlesDir, recursive: true);

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

        $filesystem->copy($testsDir.'bundles/an_example_bundle', $bundlesDir.'an_example_bundle');
        $filesystem->copy($testsDir.'bundles/bundle_two', $bundlesDir.'bundle_two');
        file_put_contents($appDir.'random.txt', 'Some random data.');
    }
}
