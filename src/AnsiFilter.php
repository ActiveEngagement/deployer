<?php

namespace Actengage\Deployer;

use Actengage\Deployer\Contracts\AnsiFilter as AnsiFilterInterface;

/**
 * Filters ANSI usage.
 *
 * An implementatino of {@see Actengage\Contracts\AnsiFilter} that simply manages whether ANSI is allowed.
 */
class AnsiFilter implements AnsiFilterInterface
{
    protected bool $allowed = true;

    public function allow(bool $allowed = true): void
    {
        $this->allowed = $allowed;
    }

    public function disallow(): void
    {
        $this->allowed = false;
    }

    public function allowed(): bool
    {
        return $this->allowed;
    }
}
