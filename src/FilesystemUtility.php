<?php

namespace Actengage\Deployer;

use FilesystemIterator;
use Illuminate\Support\Str;

use InvalidArgumentException;

class FilesystemUtility
{
    /**
     * Adapted from https://stackoverflow.com/a/15575293/4713952.
     */
    function joinPaths(string ...$paths): string
    {
        $paths = array_filter($paths, fn ($path) => $path !== '');
        return preg_replace('#/+#', '/', join('/', $paths));
    }

    function countDirChildren(string $path): int
    {
        return iterator_count(new FilesystemIterator($path, FilesystemIterator::SKIP_DOTS));
    }

    public function delete(string $path): void
    {
        if (is_dir($path)) {
            $files = glob($this->joinPaths($path, '*'), GLOB_MARK);
            foreach ($files as $file) {
                $this->delete($file);
            }
            rmdir($path);
        } else {
            unlink($path);
        }
    }

    public function copy(string $from, string $to): void
    {
        if (file_exists($to)) {
            throw new DeployerException("The destination $to exists!");
        }

        if (is_dir($from)) {
            mkdir($to, recursive: true);
            foreach (scandir($from) as $file) {
                if ($file != "." && $file != "..") {
                    $this->copy($this->joinPaths($from, $file), $this->joinPaths($to, $file));
                }
            }
        } else {
            $dir = dirname($to);
            if (!file_exists($dir)) {
                mkdir($dir, recursive: true);
            }
            copy($from, $to);
        }
    }
}