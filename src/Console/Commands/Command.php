<?php

namespace Actengage\Deployer\Console\Commands;

use Actengage\Deployer\CommandLogger;
use Illuminate\Console\Command as BaseCommand;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * The base class for all deployer commands.
 *
 * An abstract base class for all custom Artisan commands in the `deployer` package.
 */
abstract class Command extends BaseCommand
{
    public const HEAD_BROKEN_MSG = 'The deployment head is broken. We are unable to determine the currently deployed bundle. Please fix this by running "php artisan deployer --latest".';

    /**
     * Creates a logger.
     *
     * Creates an appropriate `LoggerInterface` instance for this command, based on the `verbosity` option.
     *
     * @return LoggerInterface
     */
    protected function createLogger(): LoggerInterface
    {
        $logLevel = match ((int) $this->getVerbosity()) {
            0 => LogLevel::ERROR,
            1 => LogLevel::NOTICE,
            2 => LogLevel::INFO,
            3 => LogLevel::DEBUG,
        };

        return new CommandLogger($this, $logLevel);
    }

    protected function getVerbosity(): int
    {
        $output = $this->getOutput();

        if ($output->isDebug()) {
            return 3;
        } elseif ($output->isVeryVerbose()) {
            return 2;
        } elseif ($output->isVerbose()) {
            return 2;
        } else {
            return 1;
        }
    }

    /**
     * Warns that the head is broken.
     *
     * Displays a warning on the console that the deployment head is broken, i.e. that the currently deployed bundle
     * could not be retrieved from the `HEAD` file.
     *
     * @return void
     */
    protected function warnHeadBroken(): void
    {
        $this->warn(self::HEAD_BROKEN_MSG);
    }

    /**
     * Warns in red that the head is broken.
     *
     * Displays an error on the console that the deployment head is broken, i.e. that the currently deployed bundle
     * could not be retrieved from the `HEAD` file.
     *
     * @return void
     */
    protected function errorHeadBroken(): void
    {
        $this->error(self::HEAD_BROKEN_MSG);
    }
}
