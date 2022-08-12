<?php

namespace Actengage\Deployer;

use Actengage\Deployer\Contracts\PathProvider;
use Illuminate\Support\Collection;

/**
 * Manages that current bundle.
 * 
 * A class that is capable of getting and setting the currently deployed bundle.
 * 
 * It does this by accessing a `HEAD` file in the meta directory.
 */
class CurrentBundleManager
{
    public function __construct
    (
        protected FilesystemUtility $filesystem,
        protected PathProvider $paths
    ) {
    }

    /**
     * Gets the current bundle.
     * 
     * Attempts to get the name on disk of the currently deployed bundle, as stored in the `HEAD` file.
     * 
     * @return ?string the name of the currently deployed bundle, of `null` if it could not be read.
     */
    public function get(): ?string
    {
        $headFilePath = $this->headFilePath();

        if (!file_exists($headFilePath)) {
            return null;
        }

        return trim(file_get_contents($headFilePath));
    }

    /**
     * Sets the current bundle.
     * 
     * Sets the currently deployed bundle by storing its name on disk in the `HEAD` file.
     * 
     * @param string $name the name on disk of the bundle which should be marked "currenly deployed."
     * @return void
     */
    public function set(string $name): void
    {
        file_put_contents($this->headFilePath(), $name);
    }

    /**
     * Whether the given bundle is the current one.
     * 
     * Determines whether the given bundle is the current one, as stored in the `HEAD` file.
     * 
     * @param string|Bundle $bundle the name on disk of the bundle to check or a `Bundle` instance, from which the name
     * will be retrieved.
     * @return bool whether the given bundle is the current one.
     */
    public function is(string|Bundle $bundle): bool
    {
        $name = $this->get();

        if ($bundle instanceof Bundle) {
            $bundle = $bundle->fileName();
        }

        return $name && $bundle && $bundle === $name;
    }

    /**
     * Gets the head file path.
     * 
     * Gets the full path to the `HEAD` file in which the currently deployed bundle is stored.
     * 
     * @return string
     */
    protected function headFilePath(): string
    {
        return $this->filesystem->joinPaths($this->paths->metaDir(), 'HEAD');
    }

}