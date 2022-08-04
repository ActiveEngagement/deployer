<?php

namespace Actengage\Deployer;

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
     * Creates a new instance of `ArtifactDeployer` with the given `FilesystemUtility` and backup directory path.
     * 
     * @param FilesystemUtility $filesystem a `FileSystemUtility` instance to use for various filesystem tasks.
     * @param string $backupDir the path to the directory into which to back up existing artifacts.
     * @param string $deploymentDir the path to the directory into which to deploy; all artifact rule paths are relative
     * to this path.
     */
    public function __construct(
        protected FilesystemUtility $filesystem,
        protected string $backupDir,
        protected string $deploymentDir
    )
    {
    }

    /**
     * Backs up the given artifact.
     * 
     * If the given artifact exists, then this method will copy it to the backup directory.
     * 
     * @param string $path the path (relative to the deployment root) to the artifact to back up.
     * @return void
     */
    public function backup(string $path): void
    {
        $fullPath = $this->filesystem->joinPaths($this->deploymentDir, $path);
        if (!file_exists($fullPath)) return;

        $newPath = $this->filesystem->joinPaths($this->backupDir, $path);

        # Keep up to one backup.
        if (file_exists($newPath)) {
            $this->filesystem->delete($newPath);
        }

        $this->filesystem->copy($fullPath, $newPath);
    }

    /**
     * Deploys the given artifact.
     * 
     * Moves the artifact at the given path to a new path, overwriting any existing artifact.
     * 
     * @param string $from the full path to the extracted artifact being deployed.
     * @param string $to the path (relative to the deployment root) to which the artifact should be moved.
     * @return void
     */
    public function deploy(string $from, string $to): void
    {
        $toPath = $this->filesystem->joinPaths($this->deploymentDir, $to);

        if (file_exists($to)) {
            $this->filesystem->delete($to);
        }

        $this->filesystem->copy($from, $toPath);
    }
}