<?php

namespace Actengage\Deployer;

use PharData;

/**
 * Extracts a bundle.
 * 
 * A class that is capable of extracting a .tar.gz artifact bundle into the given extraction directory.
 */
class BundleExtractor
{
    /**
     * Creates a new instance.
     * 
     * Creates a new instance of `BundleExtractor` with the given `FilesystemUtility` and path provider.
     * 
     * @param FilesystemUtility $filesystem a `FileSystemUtility` instance to use for various filesystem tasks.
     * @param IPathProvider $paths an `IPathProvider` instance used to retrieve file paths.
     */
    public function __construct(
        protected FilesystemUtility $filesystem,
        protected IPathProvider $paths
    )
    {
    }

    /**
     * Extracts a bundle.
     * 
     * Extracts a bundle with the given name from its `.tar.gz` file in the bundles directory into a directory with
     * the bundle name in the extraction directory.
     * 
     * @param string $bundleName the name of the bundle to extract.
     * @return ?string the path to the extracted bundle.
     */
    public function extract(string $bundleName): string
    {
        $fileName = $bundleName.'.tar.gz';
        $bundlePath = $this->filesystem->joinPaths($this->paths->bundlesDir(), $fileName);
        $copyPath = $this->filesystem->joinPaths($this->paths->extractionDir(), $fileName);
        $extractedPath = $this->filesystem->joinPaths($this->paths->extractionDir(), $bundleName);

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