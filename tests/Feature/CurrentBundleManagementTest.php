<?php

namespace Tests\Feature;

use Actengage\Deployer\BundlesAccessor;
use Actengage\Deployer\CurrentBundleManager;
use Actengage\Deployer\FilesystemUtility;
use Tests\TestCase;

class CurrentBundleManagementTest extends TestCase
{
    public function test()
    {
        if (file_exists($this->headPath())) {
            $this->filesystem()->delete($this->headPath());
        }

        $currentBundle = $this->makeManager();

        $this->assertNull($currentBundle->get());
        $this->assertFalse($currentBundle->is(''));
        $this->assertFalse($currentBundle->is(false));
        $this->assertFalse($currentBundle->is('some_random_name'));

        $currentBundle->set('bundle_two');
        $this->assertEquals("bundle_two\n", file_get_contents($this->headPath()));

        $this->assertEquals('bundle_two', $currentBundle->get());
        $this->assertFalse($currentBundle->is(''));
        $this->assertFalse($currentBundle->is(false));
        $this->assertFalse($currentBundle->is('some_random_name'));
        $this->assertTrue($currentBundle->is('bundle_two'));

        $all = $this->makeBundlesAccessor()->all();
        $current = $all->first(fn ($b) => $currentBundle->is($b));
        $this->assertEquals($this->testsDir().'storage/bundles/deeply/nested/bundle_two', $current->path);
        $this->assertTrue($currentBundle->is($current));
    }

    private function makeBundlesAccessor(): BundlesAccessor
    {
        return app()->make(BundlesAccessor::class);
    }

    private function makeManager(): CurrentBundleManager
    {
        return app()->make(CurrentBundleManager::class);
    }

    private function filesystem(): FilesystemUtility
    {
        return app()->make(FilesystemUtility::class);
    }

    private function headPath(): string
    {
        return $this->testsDir().'storage/app/deployer_meta/HEAD';
    }
}