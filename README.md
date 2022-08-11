# Deployer

This simple package provides an easy way to get and deploy pre-built artifacts (maybe artifacted with [our GitHub action](https://github.com/ActiveEngagement/deployer-action)) to your application.

## Requirements

- Laravel 9.x+
- PHP 8.0+

## Getting Started

Add the repository in `composer.json`:

```json
"repositories": [
  {
    "type": "vcs",
    "url": "https://github.com/ActiveEngagement/deployer.git"
  }
]
```

And require the package:

```bash
composer require actengage/deployer
```

Publish the configuration:

```bash
php artisan vendor:publish --tag=deployer-config
```

## Artifacts

`deployer` can deploy one or more "artifacts" from an "artifact bundle" directory on the filesystem. You may specify where you'd like to deploy each artifact in `config/deployer.php`:

```php
<?php

return [
  'artifacts' => [
    'some_artifact.txt' => 'path/to/destination.txt'
  ]
];
```

In this case, `deployer` will look for a file called `some_artifact.txt` inside the artifact bundle and copy it to `path/to/destination.txt`. **If `path/to/destination.txt` already exists, it will be removed.**

Directories are allowed too:

```php
<?php

return [
  'artifacts' => [
    'assets' => 'public/build/assets'
  ]
];
```

`public/build/assets` will be replaced each deployment by the `assets` directory in the bundle.

## Usage

Currently, `deployer` consists of one Artisan command:

```bash
php artisan deployer:artifacts {bundle}
```

You may invoke this command whenever you deploy your application (e.g. from your deploy script when using [Laravel Forge](https://forge.laravel.com)), giving it the name of a pre-built bundle. For example:

```
php artisan deployer:artifacts 2022-08-04
```

`deployer` will look in the bundles directory (`storage/deployer/artifact_bundles` by default) for a directory named `2022-08-04` and will deploy its artifacts.

## Configuration

You may customize the bundles directory used by `deployer` in `config/deployer.php`:

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
];
```

If any of these file paths begin with a `/`, then they are assumed to be absolute file paths and are used directly. If not, they will be resolved relative to the deployment root (which is the current directory by default).
