<?php

namespace Actengage\Deployer;

use Actengage\Deployer\Contracts\PathProvider;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Deploys an artifact.
 *
 * A class that is capable of deploying an artifact to a given path, optionally backing up any existing artifact first.
 */
class ArtifactDeployer
{
    /**
     * Creates a new instance.
     *
     * Creates a new instance of `ArtifactDeployer` with the given `FilesystemUtility` and path provider.
     *
     * @param  FilesystemUtility  $filesystem a `FileSystemUtility` instance to use for various filesystem tasks.
     * @param  PathProvider  $paths a `PathProvider` instance used to retrieve file paths.
     */
    public function __construct(
        protected FilesystemUtility $filesystem,
        protected PathProvider $paths
    ) {
    }

    /**
     * Backs up the given artifact.
     *
     * If the given artifact exists, then this method will copy it to the backup directory.
     *
     * @param  string  $path the path (relative to the deployment root) to the artifact to back up.
     * @param  LoggerInterface  $logger an optional logger.
     * @return void
     */
    public function backup(string $path, LoggerInterface $logger = new NullLogger): void
    {
        $fullPath = $this->filesystem->joinPaths($this->paths->deploymentDir(), $path);
        if (! file_exists($fullPath)) {
            return;
        }

        $newPath = $this->filesystem->joinPaths($this->paths->backupDir(), $path);

        // Keep up to one backup.
        if (file_exists($newPath)) {
            $logger->debug("Deleting previous backup at $newPath");
            $this->filesystem->delete($newPath);
        }

        $logger->info("Backing up $fullPath to $newPath");
        $this->filesystem->copy($fullPath, $newPath);
    }

    /**
     * Deploys the given artifact.
     *
     * Moves the artifact at the given path to a new path, overwriting any existing artifact.
     *
     * @param  string  $from the full path to the extracted artifact being deployed.
     * @param  string  $to the path (relative to the deployment root) to which the artifact should be moved.
     * @param  LoggerInterface  $logger an optional logger.
     * @return void
     */
    public function deploy(string $from, string $to, LoggerInterface $logger = new NullLogger): void
    {
        $toPath = $this->filesystem->joinPaths($this->paths->deploymentDir(), $to);

        if (file_exists($to)) {
            $logger->debug("Deleting existing artifact at $to");
            $this->filesystem->delete($to);
        }

        $logger->info("Deploying artifact from $from to $toPath");
        $this->filesystem->copy($from, $toPath);
    }
}
