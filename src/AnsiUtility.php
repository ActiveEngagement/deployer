<?php

namespace Actengage\Deployer;

class AnsiUtility
{
    public function __construct(protected AnsiCodeUtility $codes)
    {
    }

    public function bold(string $input): string
    {
        return $this->codes->make('1').$input.$this->codes->make('22');
    }
}