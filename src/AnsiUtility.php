<?php

namespace Actengage\Deployer;

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

    public function bold(string $input): string
    {
        return $this->code(self::BOLD_ON).$input.$this->code(self::BOLD_OFF);
    }

    /**
     * Gets an ANSI code.
     *
     * Gets an ANSI code with the given attribute "parameters."
     *
     * @param  string[]  $params the attributes for which to generate a code.
     * @return string the generated code.
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
}
