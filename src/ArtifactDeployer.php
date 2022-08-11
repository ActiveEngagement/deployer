<?php

namespace Actengage\Deployer;

use Actengage\Deployer\Contracts\LoggerRepository;
use Actengage\Deployer\Contracts\PathProvider;

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
     * @param  LoggerRepository  $logger a `LoggerRepository` that provides an optional logger for logging.
     */
    public function __construct(
        protected FilesystemUtility $filesystem,
        protected PathProvider $paths,
        protected LoggerRepository $logger
    ) {
    }

    /**
     * Deploys the given artifact.
     *
     * Moves the artifact at the given path to a new path, overwriting any existing artifact.
     *
     * @param  string  $from the full path to the extracted artifact being deployed.
     * @param  string  $to the path (relative to the deployment root) to which the artifact should be moved.
     * @return void
     */
    public function deploy(string $from, string $to): void
    {
        $toPath = $this->filesystem->joinPaths($this->paths->deploymentDir(), $to);

        if (file_exists($to)) {
            $this->logger->get()->debug("Deleting existing artifact at $to");
            $this->filesystem->delete($to);
        }

        $this->logger->get()->info("Deploying artifact from $from to $toPath");
        $this->filesystem->copy($from, $toPath);
    }
}
