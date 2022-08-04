<?php

namespace Actengage\Deployer;

use Psr\Log\AbstractLogger;
use Stringable;

class EchoLogger extends AbstractLogger
{
    public function log($level, string|Stringable $message, array $context = []): void
    {
        echo($message);
    }
}