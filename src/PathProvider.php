<?php

namespace Actengage\Deployer;

use Illuminate\Support\Str;

/**
 * Provides necessary file paths.
 * 
 * An implementation of `IPathProvider` that resolves relative file paths from the deployment root.
 */
class PathProvider implements IPathProvider
{
    public function __construct
    (
        protected FilesystemUtility $filesystem,
        protected string $bundlesDir,
        protected string $extractionDir,
        protected string $backupDir,
        protected string $deploymentDir
    )
    {
    }

    public function bundlesDir(): string
    {
        return $this->resolvePath($this->bundlesDir);
    }

    public function extractionDir(): string
    {
        return $this->resolvePath($this->extractionDir);
    }

    public function backupDir(): string
    {
        return $this->resolvePath($this->backupDir);
    }

    public function deploymentDir(): string
    {
        return $this->deploymentDir;
    }

    protected function resolvePath(string $path)
    {
        if (Str::startsWith($path, '/')) return $path;

        return $this->filesystem->joinPaths($this->deploymentDir, $path);
    }
}