<?php

namespace Tests\Feature;

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

        $currentBundle->set('current_deployment');
        $this->assertEquals("current_deployment\n", file_get_contents($this->headPath()));

        $this->assertEquals('current_deployment', $currentBundle->get());
        $this->assertFalse($currentBundle->is(''));
        $this->assertFalse($currentBundle->is(false));
        $this->assertFalse($currentBundle->is('some_random_name'));
        $this->assertTrue($currentBundle->is('current_deployment'));
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
        return $this->testsDir().'storage/bundles/deeply/HEAD';
    }
}