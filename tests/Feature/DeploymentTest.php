<?php

namespace Tests\Feature;

use Actengage\Deployer\BundleDeployer;
use Actengage\Deployer\FilesystemUtility;
use Tests\TestCase;

class DeploymentTest extends TestCase
{
    public function test()
    {
        $filesystem = app()->make(FilesystemUtility::class);
        $deployer = app()->make(BundleDeployer::class);

        // FIRST DEPLOYMENT

        $deployer->deploy($this->testsDir().'storage/bundles/deeply/nested/an_example_bundle');

        // Assert artifact1
        $this->assertDirectoryExists($this->testsDir().'storage/app/public/build');
        $this->assertEquals("Nested!\n", file_get_contents($this->testsDir().'storage/app/public/build/very/deeply/nested/nested.txt'));
        $this->assertEquals('', file_get_contents($this->testsDir().'storage/app/public/build/empty.txt'));

        // Assert artifact2.
        $this->assertDirectoryExists($this->testsDir().'storage/app/public/build2');
        $this->assertEquals('', file_get_contents($this->testsDir().'storage/app/public/build2/random.empty.txt'));

        // Assert artifact3
        $this->assertEquals("For freedom Christ has set you free.\n", file_get_contents($this->testsDir().'storage/app/unrelated/built_file.txt'));

        // SECOND DEPLOYMENT

        // Test the artifact deployment process.

        $deployer = app()->get(BundleDeployer::class);
        $deployer->deploy($this->testsDir().'storage/bundles/deeply/nested/bundle_two');

        // Assert artifact1
        $this->assertDirectoryExists($this->testsDir().'storage/app/public/build');
        $this->assertEquals(1, $filesystem->countDirChildren($this->testsDir().'storage/app/public/build'));
        $this->assertEquals('', file_get_contents($this->testsDir().'storage/app/public/build/random.empty.txt'));

        // Assert artifact2.
        $this->assertDirectoryExists($this->testsDir().'storage/app/public/build2');
        $this->assertEquals('', file_get_contents($this->testsDir().'storage/app/public/build2/random.empty.txt'));

        // Assert artifact3
        $this->assertEquals("For freedom Christ has set you free; therefore do not submit again to a yoke of slavery.\n", file_get_contents($this->testsDir().'storage/app/unrelated/built_file.txt'));
    }
}
