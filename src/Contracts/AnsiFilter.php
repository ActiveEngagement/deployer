<?php

namespace Actengage\Deployer\Contracts;

/**
 * Filters ANSI usage.
 * 
 * Defines a class that is capable of filtering the output of special ANSI codes.
 * 
 * Currently, the only filtering involves turning ANSI output on and off.
 */
interface AnsiFilter
{
    public function allow(bool $allowed = true): void;

    public function disallow(): void;

    public function allowed(): bool;
}