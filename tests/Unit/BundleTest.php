<?php

namespace Tests\Unit;

use Actengage\Deployer\Bundle;
use Carbon\Carbon;
use InvalidArgumentException;
use Tests\TestCase;

class BundleTest extends TestCase
{
    public function test__fromJson__withAllOptions()
    {
        $bundle = Bundle::fromJson('/some/random/path', json_encode([
            'commit' => 'abc',
            'initiator' => 'jll',
            'env' => 'production',
            'version' => 'v1',
            'bundled_at' => 1660327468,
            'committed_at' => 1660327484,
            'git_ref' => 'master',
            'ci_job' => 'build'
        ]));

        $this->assertEquals('/some/random/path', $bundle->path);
        $this->assertEquals('abc', $bundle->commit);
        $this->assertEquals('jll', $bundle->initiator);
        $this->assertEquals('production', $bundle->env);
        $this->assertEquals('v1', $bundle->version);
        $this->assertEquals(Carbon::parse('Fri Aug 12 2022 18:04:28 GMT+0000'), $bundle->bundled_at);
        $this->assertEquals(Carbon::parse('Fri Aug 12 2022 18:04:44 GMT+0000'), $bundle->committed_at);
        $this->assertEquals('master', $bundle->git_ref);
        $this->assertEquals('build', $bundle->ci_job);
    }

    public function test__fromJson__withoutOptional__nullifies()
    {
        $bundle = Bundle::fromJson('/some/random/path', json_encode(['bundled_at' => 1660327468]));

        $this->assertEquals('/some/random/path', $bundle->path);
        $this->assertNull($bundle->commit);
        $this->assertNull($bundle->initiator);
        $this->assertNull($bundle->env);
        $this->assertNull($bundle->version);
        $this->assertNull($bundle->committed_at);
        $this->assertNull($bundle->git_ref);
        $this->assertNull($bundle->ci_job);
    }

    public function test__fromJson__withoutRequired__throwsException()
    {
        $callback = fn () => Bundle::fromJson('/some/random/path', json_encode([]));

        $this->assertThrows($callback, InvalidArgumentException::class, Bundle::MISSING_REQUIRED_ERROR);
    }

    public function test__shortCommit__withLongCommit()
    {
        $bundle = Bundle::fromJson('/some/random/path', json_encode([
            'commit' => '123456789',
            'bundled_at' => 1660327468
        ]));

        $this->assertEquals('1234567', $bundle->shortCommit());
    }

    public function test__shortCommit__withShortCommit()
    {
        $bundle = Bundle::fromJson('/some/random/path', json_encode([
            'commit' => '123',
            'bundled_at' => 1660327468
        ]));

        $this->assertEquals('123', $bundle->shortCommit());
    }

    public function test__fileName()
    {
        $bundle = Bundle::fromJson('/some/random/path', json_encode(['bundled_at' => 1660327468]));
        $this->assertEquals('path', $bundle->fileName());
    }
}