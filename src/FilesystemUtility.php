<?php

namespace Actengage\Deployer;

use FilesystemIterator;
use Psr\Log\LoggerInterface;

/**
 * Contains various filesystem utilities.
 *
 * A class that contains various utility methods for working with the filesystem in PHP, since PHP seems to be lacking
 * some somewhat basic filesystem features.
 */
class FilesystemUtility
{
    public function __construct(protected LoggerInterface $logger)
    {
    }

    /**
     * Joins the given file paths.
     *
     * Joins the given file paths with a forward slash.
     *
     * Adapted from https://stackoverflow.com/a/15575293/4713952.
     *
     * @param  string[]  $paths the file paths to join.
     * @return string the joined file path.
     */
    public function joinPaths(string ...$paths): string
    {
        $paths = array_filter($paths, fn ($path) => $path !== '');

        return preg_replace('#/+#', '/', implode('/', $paths));
    }

    /**
     * Counts a directories children.
     *
     * Determines the number of directories and files a given directory contains.
     *
     * @param  string  $path the path to the directory whose children should be counted.
     * @return int the number of children.
     */
    public function countDirChildren(string $path): int
    {
        return iterator_count(new FilesystemIterator($path, FilesystemIterator::SKIP_DOTS));
    }

    /**
     * Deletes a file/directory.
     *
     * Recursively deletes the file or directory at the given path.
     *
     * PHP's `rmdir()` only allows deleting empty directories.
     *
     * @param  string  $path the path to the file or directory to delete.
     * @return void
     */
    public function delete(string $path): void
    {
        $this->logger->info("Recursively deleting $path");
        $this->_delete($path);
    }

    private function _delete(string $path): void
    {
        $this->logger->debug("Deleting $path");
        if (is_dir($path)) {
            $files = glob($this->joinPaths($path, '*'), GLOB_MARK);
            foreach ($files as $file) {
                $this->_delete($file);
            }
            rmdir($path);
        } else {
            unlink($path);
        }
    }

    /**
     * Copies a file or directory.
     *
     * Recursively copies the file or directory at the given path to a new location.
     *
     * PHP's `copy()` only support files and empty directories.
     *
     * @param  string  $from the path to the file or directory to copy.
     * @param  string  $to the new path to which to copy the file or directory.
     * @return void
     */
    public function copy(string $from, string $to): void
    {
        $this->logger->info("Recursively copying $from to $to");
        $this->_copy($from, $to);
    }

    private function _copy(string $from, string $to): void
    {
        $this->logger->debug("Copying $from to $to");
        if (file_exists($to)) {
            throw new DeployerException("The destination $to exists!");
        }

        if (is_dir($from)) {
            mkdir($to, recursive: true);
            foreach (scandir($from) as $file) {
                if ($file != '.' && $file != '..') {
                    $this->copy($this->joinPaths($from, $file), $this->joinPaths($to, $file));
                }
            }
        } else {
            // Create any necessary directories, if for example we're copying to /path/to/file.txt and the directory
            // "to" does not exist.
            $dir = dirname($to);
            if (! file_exists($dir)) {
                mkdir($dir, recursive: true);
            }
            copy($from, $to);
        }
    }
}
