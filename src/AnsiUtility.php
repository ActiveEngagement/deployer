<?php

namespace Actengage\Deployer;

use Actengage\Deployer\Contracts\AnsiFilter;
use InvalidArgumentException;

/**
 * ANSI codes utility.
 *
 * A class that is capable of working with, generating, and applying ANSI escape codes.
 */
class AnsiUtility
{
    public const NO_PARAMS_ERROR = 'No ANSI code params were provided!';

    public const RESET = '0';

    public const BOLD_ON = '1';

    public const BOLD_OFF = '22';

    public const RESET_COLOR = '39';

    public function __construct(protected AnsiFilter $filter)
    {
    }

    public function bold(string $input): string
    {
        return $this->coded(self::BOLD_ON, $input, self::BOLD_OFF);
    }

    public function colored(string $input, AnsiColor $color): string
    {
        return $this->coded($color->code(), $input, self::RESET_COLOR);
    }

    /**
     * Gets an ANSI code.
     *
     * Gets an ANSI code with the given attribute "parameters."
     *
     * @param  string[]  $params the attributes for which to generate a code.
     * @return string the generated code.
     *
     * @throws InvalidArgumentException if no params or all blank params were provided.
     */
    public function code(string ...$params): string
    {
        $params = array_filter($params);

        if (empty($params)) {
            throw new InvalidArgumentException(self::NO_PARAMS_ERROR);
        }

        return "\033[".implode(';', $params).'m';
    }

    /**
     * Codes the input.
     *
     * Creates a "coded" version of the given string by prepending the given "on" code, and appending the given "off"
     * code.
     *
     * If ANSI output is disabled, the plain text is returned.
     *
     * @param  string  $on the ANSI code that enables the ANSI effect.
     * @param  string  $plain the input to "code."
     * @param  string  $off the ANSI code that disables the ANSI effect.
     * @return string
     */
    protected function coded(string $on, string $plain, string $off): string
    {
        return $this->filtered($plain, $this->code($on).$plain.$this->code($off));
    }

    /**
     * Filters output.
     *
     * Returns the given plain output, if ANSI output is disabled, or the ANSI output if not.
     *
     * @return string
     */
    protected function filtered(string $plain, string $ansi): string
    {
        return $this->filter->allowed() ? $ansi : $plain;
    }
}
