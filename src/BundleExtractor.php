<?php

namespace Actengage\Deployer;

use Actengage\Deployer\Contracts\PathProvider;
use PharData;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

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
     * @param  FilesystemUtility  $filesystem a `FileSystemUtility` instance to use for various filesystem tasks.
     * @param  IPathProvider  $paths an `IPathProvider` instance used to retrieve file paths.
     */
    public function __construct(
        protected FilesystemUtility $filesystem,
        protected PathProvider $paths
    ) {
    }

    /**
     * Extracts a bundle.
     *
     * Extracts a bundle with the given name from its `.tar.gz` file in the bundles directory into a directory with
     * the bundle name in the extraction directory.
     *
     * If an extracted bundle with the given bundle name already exists, then a path to the existing extracted bundle is
     * simply returned.
     *
     * @param  string  $bundleName the name of the bundle to extract.
     * @param LoggerInterface $logger an optional logger.
     * @return string the path to the extracted bundle.
     */
    public function extract(string $bundleName, LoggerInterface $logger = new NullLogger): string
    {
        $fileName = $bundleName.'.tar.gz';
        $bundlePath = $this->filesystem->joinPaths($this->paths->bundlesDir(), $fileName);
        $copyPath = $this->filesystem->joinPaths($this->paths->extractionDir(), $fileName);
        $extractedPath = $this->filesystem->joinPaths($this->paths->extractionDir(), $bundleName);

        if (file_exists($extractedPath)) {
            $logger->info("Using already-extracted bundle at $extractedPath");
            return $extractedPath;
        }

        $logger->info("Extracting bundle from $bundlePath to $extractedPath");
        $logger->debug("Copying $bundlePath to $copyPath");
        copy($bundlePath, $copyPath);

        $logger->debug("Decompressing $copyPath");
        $phar = new PharData($copyPath);
        $tarPhar = $phar->decompress();

        $logger->debug("Dearchiving to $extractedPath");
        mkdir($extractedPath);
        $tarPhar->extractTo($extractedPath);

        return $extractedPath;
    }
}
