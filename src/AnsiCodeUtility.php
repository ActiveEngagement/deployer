<?php

namespace Actengage\Deployer;

class AnsiCodeUtility
{
    public function make(string $params): string
    {
        return "\033[".$params.'m';
    }

    public function reset(): string
    {
        return $this->make('0');
    }
}