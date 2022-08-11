<?php

namespace Actengage\Deployer;

use Actengage\Deployer\Contracts\BundlesRepository;
use Actengage\Deployer\Contracts\PathProvider;

class BundlePruner
{
    public function __construct
    (
        protected FilesystemUtility $filesystem,
        protected BundlesRepository $bundles,
        protected LoggerRepository $logger
    )
    {
    }

    public function prune(int $keep): int
    {
        $deleted = 0;

        $bundlesToDelete = $this->bundles->all()->skip($keep);
        $bundlesToDelete->each(function (Bundle $bundle) use (&$deleted) {
            $this->logger->get()->debug("Deleting bundle $bundle->path");
            $this->filesystem->delete($bundle->path);
            $deleted++;
        });

        return $deleted;
    }
}