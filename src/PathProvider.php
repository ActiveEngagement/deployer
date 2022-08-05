<?php

namespace Actengage\Deployer;

class PathProvider extends AbstractPathProvider
{
    public function __construct(FilesystemUtility $filesystem, string $deploymentDir)
    {
        parent::__construct($filesystem, $deploymentDir);
    }

    protected function unresolvedBundlesDir(): string
    {
        return config('deployer.bundles_dir');
    }

    protected function unresolvedExtractionDir(): string
    {
        return config('deployer.extraction_dir');
    }

    protected function unresolvedBackupDir(): string
    {
        return config('deployer.backup_dir');
    }
}