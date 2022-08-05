<?php

namespace Tests\Support;

use Actengage\Deployer\AbstractPathProvider;
use Actengage\Deployer\FilesystemUtility;

class PathProvider extends AbstractPathProvider
{
    public function __construct(FilesystemUtility $filesystem, protected string $testsDir)
    {
        parent::__construct($filesystem, $testsDir.'storage/app');
    }

    protected function unresolvedBundlesDir(): string
    {
        return $this->testsDir.'storage/bundles/deeply/nested';
    }

    protected function unresolvedExtractionDir(): string
    {
        return $this->testsDir.'storage/path/to/extraction/dir';
    }

    protected function unresolvedBackupDir(): string
    {
        return $this->testsDir.'storage/backups';
    }
}
