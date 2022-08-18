<?php

namespace Actengage\Deployer\Console\Commands;

use Actengage\Deployer\CommandLogger;
use Actengage\Deployer\Contracts\AnsiFilter;
use Actengage\Deployer\Contracts\LoggerRepository;
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
     * Sets up dependencies.
     * 
     * Sets up dependencies common to all commands in this package.
     * 
     * In particular, it:
     *   - sets up a logger based on the requested verbosity level, and
     *   - filters ANSI output based on the `--ansi` / `--no-ansi` flags.
     * 
     * @return void
     */
    protected function setup(): void
    {
        app()->make(LoggerRepository::class)->set($this->createLogger());
        app()->make(AnsiFilter::class)->allow($this->isAnsiAllowed());
    }

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

    /**
     * Gets whether ANSI is allowed.
     * 
     * Determines whether ANSI output is allowed for this command by checking for the presense of the `--no-ansi` flag.
     * 
     * @return bool
     */
    protected function isAnsiAllowed(): bool
    {
        return $this->getOutput()->isDecorated();
    }

    /**
     * Gets the command verbosity.
     *
     * Retrieves an integer representation of verbosity, from 0 to 3.
     *
     * Currently, this is done by using `$this->getOutput()` to determine whether `-v`, `-vv`, or `-vvv` was passed to
     * the command.
     *
     * @return int an integer representing the requested verbosity, from 0 to 3.
     */
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
