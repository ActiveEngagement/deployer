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
    /**
     * Creates a logger.
     *
     * Creates an appropriate `LoggerInterface` instance for this command, based on the `verbosity` option.
     *
     * @return LoggerInterface
     */
    protected function createLogger(): LoggerInterface
    {
        $logLevel = match ((int) $this->option('verbosity')) {
            0 => LogLevel::ERROR,
            1 => LogLevel::NOTICE,
            2 => LogLevel::INFO,
            default => LogLevel::DEBUG,
        };

        return new CommandLogger($this, $logLevel);
    }

    protected function warnHeadBroken(): void
    {
        $this->warn('The deployment head is broken. We are unable to determine the currently deployed bundle. Please fix this by running "php artisan deployer --latest".');
    }
}
