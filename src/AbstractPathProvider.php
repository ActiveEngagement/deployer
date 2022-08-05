<?php

namespace Actengage\Deployer;

use Actengage\Deployer\Contracts\PathProvider;
use Illuminate\Support\Str;

/**
 * Provides necessary file paths.
 *
 * An abstract implementation of `PathProvider` that resolves relative file paths from the deployment root.
 */
abstract class AbstractPathProvider implements PathProvider
{
    public function __construct(protected FilesystemUtility $filesystem, protected string $deploymentDir)
    {
    }

    public function bundlesDir(): string
    {
        return $this->resolvePath($this->unresolvedBundlesDir());
    }

    public function extractionDir(): string
    {
        return $this->resolvePath($this->unresolvedExtractionDir());
    }

    public function backupDir(): string
    {
        return $this->resolvePath($this->unresolvedBackupDir());
    }

    public function deploymentDir(): string
    {
        return $this->deploymentDir;
    }

    /**
     * Gets the unresolved bundles dir.
     *
     * Should get the raw, unresolved path to the bundles directory. It will be resolved from the deployment root.
     *
     * @return string
     */
    abstract protected function unresolvedBundlesDir(): string;

    /**
     * Gets the unresolved extraction dir.
     *
     * Should get the raw, unresolved path to the extraction directory. It will be resolved from the deployment root.
     *
     * @return string
     */
    abstract protected function unresolvedExtractionDir(): string;

    /**
     * Gets the unresolved backup dir.
     *
     * Should get the raw, unresolved path to the backup directory. It will be resolved from the deployment root.
     *
     * @return string
     */
    abstract protected function unresolvedBackupDir(): string;

    /**
     * Resolves the given path.
     *
     * Resolves the given path from the deployment root.
     *
     * @param  string  $path the path to resolves.
     * @return string the resolved path.
     */
    protected function resolvePath(string $path): string
    {
        if (Str::startsWith($path, '/')) {
            return $path;
        }

        return $this->filesystem->joinPaths($this->deploymentDir, $path);
    }
}
