<?php

namespace Actengage\Deployer;

use Actengage\Deployer\Contracts\PathProvider;
use Actengage\Deployer\FilesystemUtility;
use Actengage\Deployer\Contracts\BundlesRepository as BundlesRepositoryInterface;
use Illuminate\Support\LazyCollection;

class BundlesRepository implements BundlesRepositoryInterface
{
    public function __construct
    (
        protected FilesystemUtility $filesystem,
        protected PathProvider $paths
    )
    {
    }

    public function all(int $limit = null): LazyCollection
    {
        $all = LazyCollection::make(function() {
            $bundleDirs = scandir($this->paths->bundlesDir());
            foreach ($bundleDirs as $bundlePath) {
                yield $this->getBundle($bundlePath);
            }
        });

        return is_null($limit) ? $all : $all->take($limit);
    }

    protected function getBundle(string $path): Bundle
    {
        $manifestPath = $this->filesystem->joinPaths($path, 'manifest.json');
        return Bundle::fromJson(file_get_contents($manifestPath));
    }
}