<?php

namespace Actengage\Deployer;

/**
 * ANSI codes utility.
 * 
 * A class that is capable of working with, generating, and applying ANSI escape codes.
 */
class AnsiUtility
{
    public const RESET = '0';
    public const BOLD_ON = '1';
    public const BOLD_OFF = '22';

    public function bold(string $input): string
    {
        return $this->code(self::BOLD_ON).$input.$this->code(self::BOLD_OFF);
    }

    /**
     * Gets an ANSI code.
     * 
     * Gets an ANSI code with the given attribute "parameters."
     * 
     * @param string[] $params the attributes for which to generate a code.
     * @return string the generated code.
     */
    public function code(string ...$params): string
    {
        return "\033[".implode(';', $params).'m';
    }
}