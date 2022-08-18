<?php

namespace Actengage\Deployer;

use Actengage\Deployer\Contracts\LoggerRepository;
use Actengage\Deployer\Contracts\PathProvider;
use Illuminate\Support\Collection;

/**
 * Prunes old bundles.
 *
 * A class that is capable of removing old bundles from the bundles directory.
 */
class BundlePruner
{
    public function __construct(
        protected FilesystemUtility $filesystem,
        protected PathProvider $paths,
        protected BundlesAccessor $bundles,
        protected LoggerRepository $logger
    ) {
    }

    /**
     * Prunes old bundles.
     *
     * Removes all valid bundles from disk except for the given number of most recent ones to keep.
     *
     * This method can also optionally remove invalid bundles (id est, bundles with missing or malformed manifests).
     *
     * @param  int  $keep the number of most recent bundles to keep.
     * @param  bool  $includeInvalid whether to remove all invalid bundles, in addition to pruning the valid ones.
     * @return int the total number of bundles (both valid and invalid) that were deleted.
     */
    public function prune(int $keep, bool $includeInvalid = false): int
    {
        $bundles = $this->bundles->all();
        $deleted = 0;

        $deleted += $this->pruneValidBundles($bundles, $keep);
        if ($includeInvalid) {
            $deleted += $this->pruneInvalidBundles($bundles);
        }

        return $deleted;
    }

    /**
     * Prunes the given bundles.
     *
     * Removes all the given bundles from disk except for the given number of most recent ones to keep.
     *
     * @param  Collection  $bundles the bundles to prune. Note that this collection itself will not be modified; it is the files on disk that will be deleted.
     * @param  int  $keep the number of most recent bundles to keep.
     * @return int the number of bundles that were deleted.
     */
    protected function pruneValidBundles(Collection $bundles, int $keep): int
    {
        $deleted = 0;

        $bundlesToDelete = $bundles->skip($keep);
        $bundlesToDelete->each(function (Bundle $bundle) use (&$deleted) {
            $this->logger->get()->debug("Deleting bundle at $bundle->path");
            $this->filesystem->delete($bundle->path);
            $deleted++;
        });

        return $deleted;
    }

    /**
     * Prunes invalid bundles.
     *
     * Removes all invalid bundles from disk.
     *
     * Invalid bundles comprise all bundles except those whose paths exist in the given collection of valid bundles.
     *
     * @param  Collectioni  $bundles the collection of valid bundles that should *not* be removed.
     * @return int the number of invalid bundles that were deleted.
     */
    protected function pruneInvalidBundles(Collection $bundles): int
    {
        $deleted = 0;

        $this->filesystem->eachChild($this->paths->bundlesDir(), function ($file) use ($bundles, &$deleted) {
            if ($bundles->first(fn ($b) => $b->path === $file)) {
                // This is a valid bundle; skip it.
                return;
            }

            $this->logger->get()->debug("Deleting invalid bundle at $file");
            $this->filesystem->delete($file);
            $deleted++;
        });

        return $deleted;
    }
}
