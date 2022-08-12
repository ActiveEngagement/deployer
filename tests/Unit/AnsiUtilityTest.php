<?php

namespace Tests\Unit;

use Actengage\Deployer\AnsiUtility;
use InvalidArgumentException;
use Tests\TestCase;

class AnsiUtilityTests extends TestCase
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

    private function makeAnsi(): AnsiUtility
    {
        return new AnsiUtility;
    }
}