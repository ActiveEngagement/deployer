<?php

namespace App\Contracts;

use Illuminate\Support\LazyCollection;

interface BundlesRepository
{
    function all(int $limit = null): LazyCollection;
}