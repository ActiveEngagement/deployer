<?php

namespace Tests;

use Actengage\Deployer\BundleDeployer;
use Actengage\Deployer\Contracts\PathProvider as PathProviderInterface;
use Actengage\Deployer\FilesystemUtility;
use Actengage\Deployer\ServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;
use ReflectionClass;
use Tests\Support\PathProvider;

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

    protected function getPackageProviders($app)
    {
        return [
            ServiceProvider::class,
        ];
    }

    private function registerBindings(): void
    {
        $this->app->singleton(PathProviderInterface::class, PathProvider::class);

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
     *         bundle_two
     *         without_manifest
     *   app
     *     public
     *       build
     *         should
     *         be.txt
     *         backed_up
     *     deployer_meta
     *     unrelated
     *       do
     *         not
     *           touch
     *     random.txt
     *   test_parent
     *     one.txt
     *     two
     *     three
     *       does_not_count
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
        mkdir($appDir.'deployer_meta');

        $filesystem->copy($testsDir.'bundles/an_example_bundle', $bundlesDir.'an_example_bundle');
        $filesystem->copy($testsDir.'bundles/bundle_two', $bundlesDir.'bundle_two');
        $filesystem->copy($testsDir.'bundles/without_manifest', $bundlesDir.'without_manifest');
        file_put_contents($appDir.'random.txt', 'Some random data.');

        $testParentDir = $storageDir.'test_parent/';
        mkdir($testParentDir);
        file_put_contents($testParentDir.'one.txt', 'Test');
        mkdir($testParentDir.'two/');
        mkdir($testParentDir.'three/');
        mkdir($testParentDir.'three/does_not_count/');
    }
}
