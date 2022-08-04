<?php

namespace Actengage\Deployer;

use PharData;

class BundleExtractor
{
    public function __construct(
        protected FilesystemUtility $filesystem,
        protected string $bundlesDir,
        protected string $extractionDir
    )
    {
    }

    public function extract(string $bundleName): ?string
    {
        $fileName = $bundleName.'.tar.gz';
        $bundlePath = $this->filesystem->joinPaths($this->bundlesDir, $fileName);
        $copyPath = $this->filesystem->joinPaths($this->extractionDir, $fileName);
        $extractedPath = $this->filesystem->joinPaths($this->extractionDir, $bundleName);

        if (file_exists($extractedPath)) {
            throw new DeployerException("The bundle $bundleName has already been extracted!");
        }

        mkdir($extractedPath);
        copy($bundlePath, $copyPath);

        $phar = new PharData($copyPath);
        $tarPhar = $phar->decompress();
        $tarPhar->extractTo($extractedPath);

        return $extractedPath;
    }
}