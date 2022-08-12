<?php

namespace Tests\Feature;

use Actengage\Deployer\BundlesAccessor;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Tests\TestCase;

class BundleListingTest extends TestCase
{
    public function test()
    {
        $bundles = $this->getBundles();

        $this->assertEquals(2, $bundles->count());
        $this->assertEquals([0,1], $bundles->keys()->toArray());
        $this->assertEquals($this->testsDir().'storage/bundles/deeply/nested/bundle_two', $bundles->get(0)->path);
        $this->assertEquals(Carbon::parse('Fri Aug 12 2022 18:04:45 GMT+0000'), $bundles->get(0)->bundled_at);
        $this->assertEquals($this->testsDir().'storage/bundles/deeply/nested/an_example_bundle', $bundles->get(1)->path);
        $this->assertEquals(Carbon::parse('Fri Aug 12 2022 18:04:44 GMT+0000'), $bundles->get(1)->bundled_at);
    }

    private function getBundles(): Collection
    {
        return app()->make(BundlesAccessor::class)->all();
    }
}