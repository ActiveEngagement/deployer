<?php

namespace Actengage\Deployer;

/**
 * Deploys a bundle.
 *
 * A class that is capable of deploying all the artifacts in a given extracted bundle.
 */
class BundleDeployer
{
    /**
     * Creates a new instance.
     *
     * Creates a new instance of `BundleDeployer` with the given `FilesystemUtility`, `ArtifactDeployer`, and array of
     * artifact rules.
     *
     * @param  FilesystemUtility  $filesystem a `FileSystemUtility` instance to use for various filesystem tasks.
     * @param  ArtifactDeployer  $artifactDeployer an `ArtifactDeployer` instance to use to deploy individual artifacts.
     * @param  array<string,string>  $artifactRules an associative array of artifact "rules:" that is, artifact source and
     * destination paths.
     */
    public function __construct(
        protected FilesystemUtility $filesystem,
        protected ArtifactDeployer $artifactDeployer,
        protected array $artifactRules
    ) {
        $this->validateArtifactRules($artifactRules);
    }

    /**
     * Deploys a bundle.
     *
     * Deploys the artifact bundle at the given absolute path.
     *
     * All existing artifacts will be backed up *before* the new ones are deployed.
     *
     * @param  string  $bundlePath the full path to the bundle being deployed.
     */
    public function deploy(string $bundlePath)
    {
        foreach ($this->artifactRules as $from => $to) {
            $fromFullPath = $this->filesystem->joinPaths($bundlePath, $from);
            if (! file_exists($fromFullPath)) {
                continue;
            }

            $this->artifactDeployer->backup($to);
        }
        foreach ($this->artifactRules as $from => $to) {
            $fromFullPath = $this->filesystem->joinPaths($bundlePath, $from);
            if (! file_exists($fromFullPath)) {
                continue;
            }

            $this->artifactDeployer->deploy($fromFullPath, $to);
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
