<?php

namespace Actengage\Deployer;

/**
 * Capable of providing paths necessary to the package.
 *
 * An interface that defines a class that is capable of providing various file paths necessary for the package.
 */
interface IPathProvider
{
    public function bundlesDir(): string;

    public function extractionDir(): string;

    public function backupDir(): string;

    public function deploymentDir(): string;
}
