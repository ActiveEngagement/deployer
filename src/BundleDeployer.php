<?php

namespace Actengage\Deployer;

use Actengage\Deployer\Contracts\PathProvider;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Deploys a bundle.
 *
 * A class that is capable of deploying all the artifacts in a given bundle.
 */
class BundleDeployer
{
    /**
     * Creates a new instance.
     *
     * Creates a new instance of `BundleDeployer` with the given `FilesystemUtility`, `PathProvider`,
     * `ArtifactDeployer`, and array of artifact rules.
     *
     * @param  FilesystemUtility  $filesystem a `FileSystemUtility` instance to use for various filesystem tasks.
     * @param  PathProvider  $paths a `PathProvider` instance used to retrieve file paths.
     * @param  ArtifactDeployer  $artifactDeployer an `ArtifactDeployer` instance to use to deploy individual artifacts.
     * @param  array<string,string>  $artifactRules an associative array of artifact "rules:" that is, artifact source and
     * destination paths.
     */
    public function __construct(
        protected FilesystemUtility $filesystem,
        protected PathProvider $paths,
        protected ArtifactDeployer $artifactDeployer,
        protected array $artifactRules
    ) {
        $this->validateArtifactRules($artifactRules);
    }

    /**
     * Deploys a bundle.
     *
     * Deploys the artifact bundle with the given name.
     *
     * All existing artifacts will be backed up *before* the new ones are deployed.
     *
     * @param  string  $bundleName the name of the bundle (which will be retrieved from the bundles directory) to
     * deploy.
     * @param  LoggerInterface  $logger an optional logger.
     * @return void
     */
    public function deploy(string $bundleName, LoggerInterface $logger = new NullLogger): void
    {
        foreach ($this->artifactRules as $from => $to) {
            $fromFullPath = $this->filesystem->joinPaths($this->paths->bundlesDir(), $bundleName, $from);
            if (! file_exists($fromFullPath)) {
                $logger->notice("Skipping backup for $fromFullPath since it doesn't exist in the bundle.");

                continue;
            }

            $this->artifactDeployer->backup($to, $logger);
        }
        foreach ($this->artifactRules as $from => $to) {
            $fromFullPath = $this->filesystem->joinPaths($this->paths->bundlesDir(), $bundleName, $from);
            if (! file_exists($fromFullPath)) {
                $logger->notice("Skipping deployment for $fromFullPath since it doesn't exist in the bundle.");

                continue;
            }

            $this->artifactDeployer->deploy($fromFullPath, $to, $logger);
        }
    }

    /**
     * Validates the given artifact rules.
     *
     * Ensures that all the rules in the given array of artifact rules do not contain "nested paths," but are only top-
     * level files/directories.
     *
     * @param  array<string, string>  $rules the artifact rules to validate.
     * @return void
     *
     * @throws DeployerException if any invalid rules are found.
     */
    protected function validateArtifactRules(array $rules): void
    {
        foreach ($rules as $from => $to) {
            $dir = dirname($from);
            if ($dir !== '.') {
                throw new DeployerException(
                    'Nested artifact source paths are not permitted. All artifacts must have their own, top-level '.
                    'file or directory within the bundle.'
                );
            }
        }
    }
}
