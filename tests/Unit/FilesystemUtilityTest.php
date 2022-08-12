<?php

namespace Tests\Unit;

use Actengage\Deployer\DeployerException;
use Actengage\Deployer\FilesystemUtility;
use Tests\TestCase;

class FilesystemUtilityTest extends TestCase
{
    public function test__joinPaths__withOnePath__returnsIt()
    {
        $this->assertEquals('one/two/three', $this->makeFilesystem()->joinPaths('one/two/three'));
        $this->assertEquals('/one/two/three', $this->makeFilesystem()->joinPaths('/one/two/three'));
        $this->assertEquals('one/two/three/', $this->makeFilesystem()->joinPaths('one/two/three/'));
        $this->assertEquals('/one/two/three/', $this->makeFilesystem()->joinPaths('/one/two/three/'));
    }

    public function test__joinPaths__noSlashesAtJuncture()
    {
        $this->assertEquals('one/two/three/four', $this->makeFilesystem()->joinPaths('one/two', 'three/four'));
    }

    public function test__joinPaths__firstSlashAtJuncture()
    {
        $this->assertEquals('one/two/three/four', $this->makeFilesystem()->joinPaths('one/two/', 'three/four'));
    }

    public function test__joinPaths__secondSlashAtJuncture()
    {
        $this->assertEquals('one/two/three/four', $this->makeFilesystem()->joinPaths('one/two', '/three/four'));
    }

    public function test__joinPaths__bothSlashesAtJuncture()
    {
        $this->assertEquals('one/two/three/four', $this->makeFilesystem()->joinPaths('one/two/', '/three/four'));
    }

    public function test__joinPaths__leadingSlash__preserves()
    {
        $this->assertEquals('/one/two/three/four', $this->makeFilesystem()->joinPaths('/one/two/', '/three/four'));
    }

    public function test__joinPaths__trailingSlash__preserves()
    {
        $this->assertEquals('one/two/three/four/', $this->makeFilesystem()->joinPaths('one/two/', '/three/four/'));
    }

    public function test__joinPaths__leadingAndTrailingSlash__preserves()
    {
        $this->assertEquals('/one/two/three/four/', $this->makeFilesystem()->joinPaths('/one/two/', '/three/four/'));
    }

    public function test__joinPaths__withExtensions()
    {
        $this->assertEquals('/one/two/three/example.txt', $this->makeFilesystem()->joinPaths('/one/two/', '/three/example.txt'));
    }

    public function test__joinPaths__withManyPaths()
    {
        $expected = 'one/two/three/four/five/six/seven/eight.txt';
        $actual = $this->makeFilesystem()->joinPaths('one/two', 'three/four/', '/five/', 'six', 'seven/eight.txt');

        $this->assertEquals($expected, $actual);
    }

    public function test__joinPaths__withEmptyPaths__ignores()
    {
        $expected = 'one/two/three/four/';
        $actual = $this->makeFilesystem()->joinPaths('', 'one/two', '', 'three/four/', '');

        $this->assertEquals($expected, $actual);
    }

    public function test__countDirChildren()
    {
        $this->assertEquals(3, $this->makeFilesystem()->countDirChildren($this->testsDir().'storage/test_parent'));
    }

    public function test__eachChild()
    {
        $expected = [
            $this->testsDir().'storage/test_parent/one.txt',
            $this->testsDir().'storage/test_parent/three',
            $this->testsDir().'storage/test_parent/two',
        ];

        $actual = [];
        $this->makeFilesystem()->eachChild($this->testsDir().'storage/test_parent', function ($path) use (&$actual) {
            $actual[] = $path;
        });

        $this->assertEquals($expected, $actual);
    }

    public function test__delete__directory()
    {
        $this->assertTrue(file_exists($this->testsDir().'storage/test_parent'));
        $this->makeFilesystem()->delete($this->testsDir().'storage/test_parent');
        $this->assertFalse(file_exists($this->testsDir().'storage/test_parent'));
    }

    public function test__delete__file()
    {
        $this->assertTrue(file_exists($this->testsDir().'storage/test_parent/one.txt'));
        $this->makeFilesystem()->delete($this->testsDir().'storage/test_parent/one.txt');
        $this->assertFalse(file_exists($this->testsDir().'storage/test_parent/one.txt'));
    }

    public function test__copy__whenDestinationExists__throwsExceptino()
    {
        $destination = $this->testsDir().'storage/test_parent';
        $callback = fn () => $this->makeFilesystem()->copy('/some/random/path', $destination);

        $this->assertThrows($callback, DeployerException::class, "The destination $destination exists!");
    }

    public function test__copy__directory()
    {
        $from = $this->testsDir().'storage/test_parent/';
        $destination = $this->testsDir().'storage/copied/';
        $this->makeFilesystem()->copy($from, $destination);

        $this->assertTrue(is_dir($from));
        $this->assertEquals('Test', file_get_contents($from.'one.txt'));
        $this->assertTrue(is_dir($from.'two/'));
        $this->assertTrue(is_dir($from.'three/'));
        $this->assertTrue(is_dir($from.'three/does_not_count'));

        $this->assertTrue(is_dir($destination));
        $this->assertEquals('Test', file_get_contents($destination.'one.txt'));
        $this->assertTrue(is_dir($destination.'two/'));
        $this->assertTrue(is_dir($destination.'three/'));
        $this->assertTrue(is_dir($destination.'three/does_not_count'));
    }

    public function test__copy__file()
    {
        $from = $this->testsDir().'storage/test_parent/one.txt';
        $destination = $this->testsDir().'storage/test_parent/copied.txt';
        $this->makeFilesystem()->copy($from, $destination);

        $this->assertEquals('Test', file_get_contents($from));
        $this->assertEquals('Test', file_get_contents($destination));
    }

    public function test__copy__toAbsentDirectory__creates()
    {
        $from = $this->testsDir().'storage/test_parent/one.txt';
        $destination = $this->testsDir().'storage/test_parent/deeply/nested/copied.txt';
        $this->makeFilesystem()->copy($from, $destination);

        $this->assertEquals('Test', file_get_contents($from));
        $this->assertEquals('Test', file_get_contents($destination));
    }

    private function makeFilesystem(): FilesystemUtility
    {
        return new FilesystemUtility;
    }
}
