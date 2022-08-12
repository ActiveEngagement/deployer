<?php

namespace Actengage\Deployer;

use Actengage\Deployer\Contracts\LoggerRepository;
use Actengage\Deployer\Contracts\PathProvider;

/**
 * Deploys a bundle.
 *
 * A class that is capable of deploying all the artifacts in a given bundle.
 */
class BundleDeployer
{
    public const INVALID_RULE_ERROR = 'Nested artifact source paths are not permitted. All artifacts must have their own, top-level file or directory within the bundle.';

    /**
     * Creates a new instance.
     *
     * Creates a new instance of `BundleDeployer` with the given `FilesystemUtility`, `PathProvider`,
     * `ArtifactDeployer`, and array of artifact rules.
     *
     * @param  FilesystemUtility  $filesystem a `FileSystemUtility` instance to use for various filesystem tasks.
     * @param  LoggerRepository  $logger a `LoggerRepository` that provides an optional logger for logging.
     * @param  PathProvider  $paths a `PathProvider` instance used to retrieve file paths.
     * @param  ArtifactDeployer  $artifactDeployer an `ArtifactDeployer` instance to use to deploy individual artifacts.
     * @param  array<string,string>  $artifactRules an associative array of artifact "rules:" that is, artifact source and
     * destination paths.
     */
    public function __construct(
        protected FilesystemUtility $filesystem,
        protected PathProvider $paths,
        protected LoggerRepository $logger,
        protected ArtifactDeployer $artifactDeployer,
        protected CurrentBundleManager $currentBundle,
        protected array $artifactRules
    ) {
        $this->validateArtifactRules($artifactRules);
    }

    /**
     * Deploys a bundle.
     *
     * Deploys the artifact bundle at the given path.
     *
     * @param  string  $bundlePath  the full path to the bundle to deploy.
     * @return void
     */
    public function deploy(string $bundlePath): void
    {
        foreach ($this->artifactRules as $from => $to) {
            $fromFullPath = $this->filesystem->joinPaths($bundlePath, $from);
            if (! file_exists($fromFullPath)) {
                $this->logger->get()->notice("Skipping deployment for $fromFullPath since it doesn't exist in the bundle.");

                continue;
            }

            $this->artifactDeployer->deploy($fromFullPath, $to);
        }
        $this->currentBundle->set(basename($bundlePath));
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
                throw new DeployerException(self::INVALID_RULE_ERROR);
            }
        }
    }
}
