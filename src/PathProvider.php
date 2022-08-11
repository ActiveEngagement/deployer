<?php

namespace Actengage\Deployer;

class PathProvider extends AbstractPathProvider
{
    protected function unresolvedBundlesDir(): string
    {
        return config('deployer.bundles_dir');
    }
}
