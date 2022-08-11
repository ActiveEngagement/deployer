<?php

namespace Actengage\Deployer\Contracts;

use Illuminate\Support\Collection;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

interface BundlesRepository
{
    function all(int $limit = null, LoggerInterface $logger = new NullLogger): Collection;
}