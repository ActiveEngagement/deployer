<?php

namespace Actengage\Deployer;

use Illuminate\Console\Command;
use Psr\Log\AbstractLogger;
use Stringable;
use Illuminate\Support\Str;
use Psr\Log\LogLevel;

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
    public function __construct(protected Command $cmd, protected string $level)
    {
    }

    public function log($level, string|Stringable $message, array $context = []): void
    {
        if ($this->getLevelPriority($level) > $this->getLevelPriority($this->level)) {
            return;
        }

        $this->writeMessage($level, $this->formatMessage($level, $message));
    }

    protected function writeMessage(string $level, string $message): void
    {
        $priority = $this->getLevelPriority($level);

        if ($priority <= 3) {
            $this->cmd->error($message);
        } else if ($level === 4) {
            $this->cmd->warn($message);
        } else if ($level === 6) {
            $this->cmd->info($message);
        } else {
            $this->cmd->line($message);
        }
    }

    protected function formatMessage(string $level, string|Stringable $message): string
    {
        $levelString = Str::upper($level);

        return "[$levelString] $message";
    }

    protected function getLevelPriority(string $level): int
    {
        return match ($level) {
            LogLevel::EMERGENCY => 0,
            LogLevel::ALERT => 1,
            LogLevel::CRITICAL => 2,
            LogLevel::ERROR => 3,
            LogLevel::WARNING => 4,
            LogLevel::NOTICE => 5,
            LogLevel::INFO => 6,
            LogLevel::DEBUG => 7
        };
    }
}