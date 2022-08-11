<?php

namespace Actengage\Deployer;

use Actengage\Deployer\Contracts\LoggerRepository as LoggerRepositoryInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class LoggerRepository implements LoggerRepositoryInterface
{
    protected ?LoggerInterface $logger = null;

    public function get(): LoggerInterface
    {
        return $logger ?? new NullLogger;
    }

    public function set(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}