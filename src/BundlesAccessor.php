<?php

namespace Actengage\Deployer;

use Actengage\Deployer\Contracts\BundlesRepository as BundlesRepositoryInterface;
use Actengage\Deployer\Contracts\LoggerRepository;
use Actengage\Deployer\Contracts\PathProvider;
use Illuminate\Support\Collection;

/**
 * Accesses bundles.
 * 
 * A class that is capable of getting a list of bundles in the bundles directory and their metadata.
 */
class BundlesAccessor
{
    public function __construct(
        protected FilesystemUtility $filesystem,
        protected PathProvider $paths,
        protected LoggerRepository $logger
    ) {
    }

    /**
     * Gets all bundles.
     *
     * Gets a collection containing `Bundle` instances for every bundle in the bundles directory.
     *
     * Bundles without a `manifest.json` are skipped (a log INFO is generated).
     *
     * Bundles are sorted in descending order by datetime.
     *
     * @param  int  $limit if given, specifies the maximum number of bundles to return.
     * @return Collection
     */
    public function all(int $limit = null): Collection
    {
        $bundles = collect();

        $this->filesystem->eachChild($this->paths->bundlesDir(), function ($bundlePath) use ($bundles) {
            $bundle = $this->getBundle($bundlePath);

            if ($bundle) {
                $bundles->add($bundle);
            } else {
                $this->logger->get()->info("Skipping $bundlePath because its manifest file was missing or malformed.");
            }
        });

        $bundles = $bundles->sortByDesc->bundled_at;

        if (! is_null($limit)) {
            $bundles = $bundles->take($limit);
        }

        return $bundles->values();
    }

    public function currentName(): ?string
    {
        $headFilePath = $this->filesystem->joinPaths($this->paths->metaDir(), 'HEAD');

        if (!file_exists($headFilePath)) {
            return null;
        }

        return trim(file_get_contents($headFilePath));
    }

    public function current(): ?Bundle
    {
        $name = $this->currentName();

        if (! $name) {
            return null;
        }

        return $this->all()->first(fn ($b) => $b->fileName() === $name);
    }

    /**
     * Gets bundle metadata for the given bundle.
     *
     * Attempts to read the manifest of the bundle at the given path and returns an appropriate `Bundle` instance. If
     * the manifest was missing or malformed, `null` is returned.
     *
     * @param  string  $path the full path to the bundle.
     * @return ?Bundle a `Bundle` instance containing the bundle's metadata, or `null` if it could not be read.
     */
    protected function getBundle(string $path): ?Bundle
    {
        $manifestPath = $this->filesystem->joinPaths($path, 'manifest.json');

        if (! file_exists($manifestPath)) {
            return null;
        }

        return Bundle::fromJson($path, file_get_contents($manifestPath));
    }
}