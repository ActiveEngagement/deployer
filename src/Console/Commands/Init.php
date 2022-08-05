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
            mkdir($backupDir);
            $this->info("Created backup directory $backupDir");
        }

        $extractionDir = $paths->extractionDir();
        if (file_exists($extractionDir)) {
            $this->warn("Extraction directory exists: $extractionDir");
        } else {
            mkdir($extractionDir);
            $this->info("Created extraction directory $extractionDir");
        }

        $bundlesDir = $paths->bundlesDir();
        if (file_exists($bundlesDir)) {
            $this->warn("Bundles directory exists: $bundlesDir");
        } else {
            mkdir($bundlesDir);
            $this->info("Created bundles directory $bundlesDir");
        }

        return 0;
    }
}
