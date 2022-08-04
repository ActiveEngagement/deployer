<?php

namespace Actengage\Deployer;

class BundleDeployer
{
    public function __construct(
        protected FilesystemUtility $filesystem,
        protected ArtifactDeployer $artifactDeployer,
        protected array $artifactRules
    )
    {
    }

    public function deploy(string $bundlePath)
    {
        foreach ($this->artifactRules as $from => $to) {
            $fromFullPath = $this->filesystem->joinPaths($bundlePath, $from);
            if (!file_exists($fromFullPath)) continue;

            $this->artifactDeployer->backup(getcwd(), $to);
        }
        foreach ($this->artifactRules as $from => $to) {
            $fromFullPath = $this->filesystem->joinPaths($bundlePath, $from);
            if (!file_exists($fromFullPath)) continue;

            $this->artifactDeployer->deploy($fromFullPath, $to);
        }
    }
    
    protected function validateArtifactFules($rules)
    {
        foreach ($rules as $from => $to) {
            $dir = dirname($from);
            if ($dir !== '.') {
                throw new DeployerException(
                    'Nested artifact source paths are not permitted. All artifacts must have their own, top-level ' .
                    'file or directory within the bundle.'
                );
            }
        }
    }
}