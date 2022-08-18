<?php

namespace Tests\Unit;

use Actengage\Deployer\AnsiColor;
use Actengage\Deployer\AnsiFilter;
use Actengage\Deployer\AnsiUtility;
use InvalidArgumentException;
use Tests\TestCase;

class AnsiUtilityTest extends TestCase
{
    public function test__code__withOneParam()
    {
        $expected = "\033[1879m";
        $actual = $this->makeAnsi()->code('1879');

        $this->assertEquals($expected, $actual);
    }

    public function test__code__withMultipleParams()
    {
        $expected = "\033[1879;167;23m";
        $actual = $this->makeAnsi()->code('1879', '167', '23');

        $this->assertEquals($expected, $actual);
    }

    public function test__code__withBlankParams__filters()
    {
        $expected = "\033[ ;1879;167;23m";
        $actual = $this->makeAnsi()->code(' ', '1879', '', '167', false, '23');

        $this->assertEquals($expected, $actual);
    }

    public function test__code__withNoParams()
    {
        $callback = fn () => $this->makeAnsi()->code();

        $this->assertThrows($callback, InvalidArgumentException::class, AnsiUtility::NO_PARAMS_ERROR);
    }

    public function test__code__withAllBlankParams()
    {
        $callback = fn () => $this->makeAnsi()->code('', 0, false);

        $this->assertThrows($callback, InvalidArgumentException::class, AnsiUtility::NO_PARAMS_ERROR);
    }

    public function test__bold()
    {
        $expected = "\033[1mhi there\033[22m";
        $actual = $this->makeAnsi()->bold('hi there');

        $this->assertEquals($expected, $actual);
    }

    public function test__colored()
    {
        $expected = "\033[32mhi there\033[39m";
        $actual = $this->makeAnsi()->colored('hi there', AnsiColor::GREEN);

        $this->assertEquals($expected, $actual);
    }

    public function test__bold__filtered()
    {
        $expected = "original input";
        $actual = $this->makeAnsi(false)->bold("original input");

        $this->assertEquals($expected, $actual);
    }

    public function test__colored__filtered()
    {
        $expected = "original input";
        $actual = $this->makeAnsi(false)->colored("original input", AnsiColor::RED);

        $this->assertEquals($expected, $actual);
    }

    private function makeAnsi(bool $allowed = true): AnsiUtility
    {
        $filter = new AnsiFilter;
        $filter->allow($allowed);

        return new AnsiUtility($filter);
    }
}
