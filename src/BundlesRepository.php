<?php

namespace Actengage\Deployer;

use Actengage\Deployer\Contracts\PathProvider;
use Actengage\Deployer\FilesystemUtility;
use Actengage\Deployer\Contracts\BundlesRepository as BundlesRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class BundlesRepository implements BundlesRepositoryInterface
{
    public function __construct
    (
        protected FilesystemUtility $filesystem,
        protected PathProvider $paths
    )
    {
    }

    public function all(int $limit = null, LoggerInterface $logger = new NullLogger): Collection
    {
        $all = $this->getBundles($logger);

        if (! is_null($limit)) {
            $all = $all->sortByDesc->bundled_at;
        }

        return $all->sortByDesc->bundled_at;
    }

    protected function getBundles(LoggerInterface $logger): Collection
    {
        $bundles = collect();

        $bundleDirs = glob($this->filesystem->joinPaths($this->paths->bundlesDir(), '*'));

        foreach ($bundleDirs as $bundlePath) {
            $bundle = $this->getBundle($bundlePath);

            if ($bundle) {
                $bundles->add($bundle);
            } else {
                $logger->info("Skipping $bundlePath because its manifest file was missing or malformed.");
            }
        }

        return $bundles;
    }

    protected function getBundle(string $path): ?Bundle
    {
        $manifestPath = $this->filesystem->joinPaths($path, 'manifest.json');

        if (!file_exists($manifestPath)) {
            return null;
        }

        return Bundle::fromJson(file_get_contents($manifestPath));
    }
}