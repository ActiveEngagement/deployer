<?php

namespace Actengage\Deployer;

class PathProvider extends AbstractPathProvider
{
    protected function unresolvedBundlesDir(): string
    {
        return config('deployer.bundles_dir');
    }

    protected function unresolvedMetaDir(): string
    {
        return config('deployer.meta_dir');
    }
}
