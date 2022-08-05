<?php

namespace Actengage\Deployer\Console\Commands;

use Actengage\Deployer\Contracts\PathProvider;
use Illuminate\Console\Command;

/**
 * A command that initializes the file system.
 *
 * A custom Artisan command that creates the directories required for `deployer` to function.
 */
final class Init extends Command
{
    protected $signature = 'deployer:init';

    protected $description = 'Initializes the directories necessay for deployer to run.';

    public function handle(PathProvider $paths): int
    {
        $backupDir = $paths->backupDir();
        if (file_exists($backupDir)) {
            $this->warn("Backup directory exists: $backupDir");
        } else {
            $this->info("Creating backup directory $backupDir");
            mkdir($backupDir, recursive: true);
        }

        $extractionDir = $paths->extractionDir();
        if (file_exists($extractionDir)) {
            $this->warn("Extraction directory exists: $extractionDir");
        } else {
            $this->info("Creating extraction directory $extractionDir");
            mkdir($extractionDir, recursive: true);
        }

        $bundlesDir = $paths->bundlesDir();
        if (file_exists($bundlesDir)) {
            $this->warn("Bundles directory exists: $bundlesDir");
        } else {
            $this->info("Creating bundles directory $bundlesDir");
            mkdir($bundlesDir, recursive: true);
        }

        return 0;
    }
}
