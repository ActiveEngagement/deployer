<?php

namespace Actengage\Deployer;

use Actengage\Deployer\Contracts\PathProvider;
use Actengage\Deployer\FilesystemUtility;
use Actengage\Deployer\Contracts\BundlesRepository as BundlesRepositoryInterface;
use Actengage\Deployer\Contracts\LoggerRepository;
use Illuminate\Support\Collection;

class BundlesRepository implements BundlesRepositoryInterface
{
    public function __construct
    (
        protected FilesystemUtility $filesystem,
        protected PathProvider $paths,
        protected LoggerRepository $logger
    )
    {
    }

    public function all(int $limit = null): Collection
    {
        $bundles = collect();

        $bundlePaths = glob($this->filesystem->joinPaths($this->paths->bundlesDir(), '*'));

        foreach ($bundlePaths as $bundlePath) {
            $bundle = $this->getBundle($bundlePath);

            if ($bundle) {
                $bundles->add($bundle);
            } else {
                $this->logger->get()->info("Skipping $bundlePath because its manifest file was missing or malformed.");
            }
        }

        if (! is_null($limit)) {
            $bundles = $bundles->take($limit);
        }

        return $bundles->sortByDesc->bundled_at;
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