<?php

namespace Actengage\Deployer;

use Actengage\Deployer\Contracts\BundlesRepository as BundlesRepositoryInterface;
use Actengage\Deployer\Contracts\LoggerRepository;
use Actengage\Deployer\Contracts\PathProvider;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * The package bundles repository.
 *
 * An implementation of {@see Actengage\Deployer\Contracts\BundlesRepository} that traverses the bundles directory and
 * reads bundle metadata from it.
 */
class BundlesRepository implements BundlesRepositoryInterface
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

        return $this->withLimit($bundles->sortByDesc->bundled_at, $limit);
    }

    public function whereVersion(string $version, ?int $limit = null): Collection
    {
        return $this->withLimit($this->all()->filter(fn (Bundle $b) => $b->version === $version), $limit);
    }

    public function whereCommit(string $commit, ?int $limit = null): Collection
    {
        return $this->withLimit($this->all()->filter(fn (Bundle $b) => Str::startsWith($b->commit, $commit)), $limit);
    }

    protected function withLimit(Collection $collection, ?int $limit): Collection
    {
        return is_null($limit) ? $collection : $collection->take($limit);
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
