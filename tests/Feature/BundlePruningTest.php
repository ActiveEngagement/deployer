<?php

namespace Tests\Feature;

use Actengage\Deployer\BundlePruner;
use Actengage\Deployer\FilesystemUtility;
use Tests\TestCase;

class BundlePruningTest extends TestCase
{
    public function test__keepNone()
    {
        $pruner = $this->makePruner();

        $deleted = $pruner->prune(0);

        $this->assertEquals(2, $deleted);
        $this->assertEquals(1, $this->filesystem()->countDirChildren($this->bundlesDir()));
        $this->assertTrue(file_exists($this->bundlesDir().'without_manifest'));
    }

    public function test__keepOne()
    {
        $pruner = $this->makePruner();

        $deleted = $pruner->prune(1);

        $this->assertEquals(1, $deleted);
        $this->assertEquals(2, $this->filesystem()->countDirChildren($this->bundlesDir()));
        $this->assertTrue(file_exists($this->bundlesDir().'without_manifest'));
        $this->assertTrue(file_exists($this->bundlesDir().'bundle_two'));
    }

    private function bundlesDir(): string
    {
        return $this->testsDir().'storage/bundles/deeply/nested/';
    }

    private function filesystem(): FilesystemUtility
    {
        return app()->make(FilesystemUtility::class);
    }

    private function makePruner(): BundlePruner
    {
        return app()->make(BundlePruner::class);
    }
}
