# Deployer

This simple package provides an easy way to get, extract, and deploy pre-built artifacts (maybe artifacted with our GitHub action—coming soon!) to your application.

## Requirements

- Laravel 9.x+
- PHP 8.0+

## Getting Started

Install with Composer:

```bash
composer require actengage/deployer
```

Publish the configuration:

```bash
php artisan vendor:publish --tag=config
```

## Artifacts

`deployer` can deploy one or more "artifacts" from a `.tar.gz` bundle on the filesystem. You may specify where you'd like to deploy each artifact in `config/deployer.php`:

```php
<?php

return [
  'artifacts' => [
    'some_artifact.txt' => 'path/to/destination.txt'
  ]
];
```

In this case, `deployer` will look for a file called `some_artifact.txt` inside the artifact bundle and copy it to `path/to/destination.txt`. If `path/to/destination.txt` already exists, it will first be backed up to the backup folder.

Directories are allowed too:

```php
<?php

return [
  'artifacts' => [
    'assets' => 'public/build/assets'
  ]
];
```

`public/build/assets` will be replaced each deployment by the `assets` directory in the bundle (after being backed up).

## Usage

Currently, `deployer` consists of one Artisan command:

```bash
php artisan artifacts:get {bundle}
```

You may invoke this command whenever you deploy your application (e.g. from your deploy script when using [Laravel Forge](https://forge.laravel.com)), giving it the name of a pre-built bundle. For example:

```
php artisan artifacts:get 2022-08-04
```

`deployer` will look in the bundles directory (`storage/artifact_bundles` by default) for a file named `2022-08-04.tar.gz`, extract it to the extraction directory, then proceed to deploy its artifacts.

## Backups

Before overwriting an existing file or directory during an artifact deployment, `deployer` will first back it up to the backup directory in case the deployment fails. The file structure within the backup directory mirrors that of the deployment root (which is the current directory by default).

For example, if the directory 'public/build/assets' is about to be overwriten, with the default configuration, `deployer` will move it to `storage/old_artifacts/public/build/assets` first.

Only the last version of an artifact before the deployment is preserved; previous backups are overwriten.

## Configuration

You may customize the various directories used by `deployer` in `config/deployer.php`:

```php
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Bundles Directory
    |--------------------------------------------------------------------------
    |
    | This value is the path (absolute or relative to the deployment root) to
    | the directory where the original, compressed .tar.gz bundles are stored.
    */

    'bundles_dir' => env('DEPLOYER_BUNDLES_DIR', 'storage/artifact_bundles'),

    /*
    |--------------------------------------------------------------------------
    | Extraction Directory
    |--------------------------------------------------------------------------
    |
    | This value is the path (absolute or relative to the deployment root) to a
    | directory into which the bundles may be extracted. The extraction files
    | are temporary and may be purged from the directory when the deployment is
    | complete.
    */

    'extraction_dir' => env('DEPLOYER_EXTRACTION_DIR', 'storage/extracted_bundles'),

    /*
    |--------------------------------------------------------------------------
    | Backup Directory
    |--------------------------------------------------------------------------
    |
    | This value is the path (absolute or relative to the deployment root) to
    | the directory to where existing artifacts should be backed up before the
    | deployment overwrites them.
    */

    'backup_dir' => env('DEPLOYER_BACKUP_DIR', 'storage/old_artifacts'),
];
```

If any of these file paths begin with a `/`, then they are assumed to be absolute file paths and are used directly. If not, they will be resolved relative to the deployment root (which is the current directory by default).
