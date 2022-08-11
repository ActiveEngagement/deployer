<?php

namespace Actengage\Deployer\Console\Commands;

use Actengage\Deployer\CommandLogger;
use Illuminate\Console\Command as BaseCommand;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

abstract class Command extends BaseCommand
{
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
}