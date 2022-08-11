<?php

namespace Actengage\Deployer\Contracts;

use Psr\Log\LoggerInterface;

/**
 * Repository for a logger.
 * 
 * Defines a class that is capable of storing and accessing a `LoggerInterface`.
 */
interface LoggerRepository
{
    /**
     * Should get the logger.
     * 
     * @return LoggerInterface
     */
    function get(): LoggerInterface;

    /**
     * Should set the logger.
     * 
     * @param LoggerInterface $logger
     * @return void
     */
    function set(LoggerInterface $logger): void;
}