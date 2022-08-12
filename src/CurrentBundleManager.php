<?php

namespace Actengage\Deployer;

use Actengage\Deployer\Contracts\PathProvider;
use Illuminate\Support\Collection;

class CurrentBundleManager
{
    public function __construct
    (
        protected FilesystemUtility $filesystem,
        protected PathProvider $paths
    ) {
    }

    public function get(): ?string
    {
        $headFilePath = $this->headFilePath();

        if (!file_exists($headFilePath)) {
            return null;
        }

        return trim(file_get_contents($headFilePath));
    }

    public function set(string $name): void
    {
        file_put_contents($this->headFilePath(), $name);
    }

    public function findIn(Collection $bundles): ?Bundle
    {
        $name = $this->get();

        if (! $name) {
            return null;
        }

        return $bundles->first(fn ($b) => $b->fileName() === $name);
    }

    protected function headFilePath(): string
    {
        return $this->filesystem->joinPaths($this->paths->metaDir(), 'HEAD');
    }

}