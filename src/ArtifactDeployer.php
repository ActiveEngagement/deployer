<?php

namespace Actengage\Deployer;

class ArtifactDeployer
{
    public function __construct(
        protected FilesystemUtility $filesystem,
        protected string $backupDir
    )
    {
    }

    public function deploy(string $from, string $to): void
    {
        if (file_exists($to)) {
            $this->filesystem->delete($to);
        }

        $this->filesystem->copy($from, $to);
    }

    public function backup(string $dir, string $file): void
    {
        $path = $this->filesystem->joinPaths($dir, $file);
        $newPath = $this->filesystem->joinPaths($this->backupDir, $file);

        if (!file_exists($path)) {
            return;
        }

        # Only keep up to one backup.
        if (file_exists($newPath)) {
            $this->filesystem->delete($newPath);
        }

        $this->filesystem->copy($path, $newPath);
    }
}