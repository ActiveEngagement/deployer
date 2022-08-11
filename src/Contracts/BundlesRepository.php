<?php

namespace Actengage\Deployer\Contracts;

use Illuminate\Support\Collection;

/**
 * Repository for bundles.
 *
 * An interface that defines a class that is capable of accessing a collection of `Bundle` instances.
 *
 * Currently, this interface has a single method, `all()`. In the future, other methods with more specific filtering
 * capabilities will likely be added.
 */
interface BundlesRepository
{
    /**
     * Should get all bundles.
     *
     * Should get a collection containing all the `Bundle` instances in this bundle repository.
     *
     * The bundles should be sorted by timestamp in descending order.
     *
     * @param  int  $limit if given, specifies the maximum number of bundles to return.
     * @return Collection
     */
    public function all(int $limit = null): Collection;

    public function whereVersion(string $version, int $limit = null): Collection;

    public function whereCommit(string $commit, int $limit = null): Collection;
}
