<?php

namespace Actengage\Deployer;

use Illuminate\Console\Command;
use Psr\Log\AbstractLogger;
use Stringable;

/**
 * Logs to an Artisan command.
 * 
 * An implementation of `LoggerInterface` that writes to Artisan `Command` output.
 */
class CommandLogger extends AbstractLogger
{
    /**
     * Creates a new instance.
     * 
     * Creates a new instance of `CommandLogger` with the given Artisan `Command` and log level.
     * 
     * @param Command $cmd the Artisan command whose output should be written to.
     * @param int $level the minimum log level on which to write.
     */
    public function __construct(protected Command $cmd, protected int $level)
    {
    }

    public function log($level, string|Stringable $message, array $context = []): void
    {
        if ($level > $this->level) {
            return;
        }

        $this->writeMessage($level, $this->formatMessage($level, $message));
    }

    protected function writeMessage(int $level, string $message): void
    {
        if ($level <= LOG_ERR) {
            $this->cmd->error($message);
        } else if ($level === LOG_WARNING) {
            $this->cmd->warn($message);
        } else if ($level === LOG_INFO) {
            $this->cmd->info($message);
        } else {
            $this->cmd->line($message);
        }
    }

    protected function formatMessage(int $level, string|Stringable $message): string
    {
        $levelString = $this->getLevelString($level);
        $message = $message->__toString();

        return "deployer [$levelString] $message";
    }

    protected function getLevelString(int $level): string
    {
        return match ($level) {
            LOG_EMERG => 'EMERGENCY',
            LOG_ALERT => 'ALERT',
            LOG_CRIT => 'CRITICAL',
            LOG_ERR => 'ERROR',
            LOG_WARNING => 'WARNING',
            LOG_NOTICE => 'NOTICE',
            LOG_INFO => 'INFO',
            LOG_DEBUG => 'DEBUG'
        };
    }
}