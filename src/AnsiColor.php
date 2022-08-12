<?php

namespace Actengage\Deployer;

enum AnsiColor
{
    case BLACK;
    case RED;
    case GREEN;
    case YELLOW;
    case BLUE;
    case MAGENTA;
    case CYAN;
    case WHITE;

    public function code(): string
    {
        return match ($this) {
            self::BLACK => '30',
            self::RED => '31',
            self::GREEN => '32',
            self::YELLOW => '33',
            self::BLUE => '34',
            self::MAGENTA => '35',
            self::CYAN => '36',
            self::WHITE => '37'
        };
    }
}
