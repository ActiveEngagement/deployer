<?php

namespace Actengage\Deployer;

use Actengage\Deployer\Contracts\BundlesRepository;
use Actengage\Deployer\Contracts\LoggerRepository;

/**
 * Prunes old bundles.
 *
 * A class that is capable of removing old bundles from the bundles directory.
 */
class BundlePruner
{
    public function __construct(
        protected FilesystemUtility $filesystem,
        protected BundlesRepository $bundles,
        protected LoggerRepository $logger
    ) {
    }

    /**
     * Prunes old bundles.
     *
     * Removes all bundles from disk except for the given number of most recent ones to keep.
     *
     * Note that bundles with missing or malformed manifests will not be included (because it is impossible to
     * accurately determine their timestamps).
     *
     * @param  int  $keep the number of most recent bundles to keep.
     * @return int the number of bundles that were deleted.
     */
    public function prune(int $keep): int
    {
        $deleted = 0;

        $bundlesToDelete = $this->bundles->all()->skip($keep);
        $bundlesToDelete->each(function (Bundle $bundle) use (&$deleted) {
            $this->logger->get()->debug("Deleting bundle at $bundle->path");
            $this->filesystem->delete($bundle->path);
            $deleted++;
        });

        return $deleted;
    }
}
