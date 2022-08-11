<?php

namespace Actengage\Deployer\Contracts;

use Psr\Log\LoggerInterface;

interface LoggerRepository
{
    function get(): LoggerInterface;
    function set(LoggerInterface $logger): void;
}