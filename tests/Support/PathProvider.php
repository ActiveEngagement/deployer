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

    protected function unresolvedMetaDir(): string
    {
        return 'deployer_meta';
    }
}
