# Deployer

This simple package provides an easy way to deploy pre-built artifacts (maybe artifacted with [our GitHub action](https://github.com/ActiveEngagement/deployer-action)) to your application.

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

## Deployments

```bash
php artisan deployer [options]
```

Use the `deployer` artisan command to initiate a bundle deployment. `deployer` will copy all the artifacts in the given
bundle to your app, according to the [artifact rules](#artifacts) in `deployer.php`.

There are a number of ways to specify the artifact bundle:

### The Latest Bundle

```bash
php artisan deployer --latest
```

The easiest way to deploy a bundle is with the `--latest` flag. `deployer` will scan the bundles directory, parse each bundle's metadata, and deploy the artifacts in the bundle with the latest `bundled_at` time to your application.

### Specific Bundles

```bash
php artisan deployer --commit 7b9be90

php artisan deployer --bundle-version v1.1.0

php artisan deployer --number 3
```

If you wish to deploy a specific bundle, you have three options. If a Git commit SHA was included in the [bundle manifest](#bundle-structure), you may specify the bundle by SHA with the `--commit` flag. Or, if a version string is present in the bundle manifest, you may specify the bundle version with the `--bundle-version` flag. Finally, you may also specify the `--number` of the bundle, with `0` being the latest, `5` being the one 5 deployments ago, and so on.

Before deploying a specific bundle, you may wish to use [`deployer:list`](#deployer-list) to view available bundles.

### The Current Bundle

```bash
php artisan deployer --current
```

In certain rare situations, you may need to re-deploy the current bundle (e.g., if the files in the app get deleted or corrupted). In that case, you may use the `--current` flag, which simply re-deploys the last deployed bundle.

## Rollbacks

```bash
php artisan deployer:rollback

php artisan deployer:rollback 3
```

The `deployer:rollback` command allows you to quickly rollback to a previous deployment without manually tracing down
commit SHAs and version strings. Invoked without an argument, `deployer:rollback` will deploy the bundle that is one
chronological step before the current one. It may also be given the number of steps to go back.

It is important to note that there is no functional difference between `deployer` and `deployer:rollback`: they do the
same exact thing—deploy an artifact bundle to your app. `deployer:rollback` merely allows specifying a bundle relative
to the current one.

## The Current State

`deployer` provides two commands for getting a picture of the current deployment state: `deployer:status` and
`deployer:list`.

### `deployer:status`

```
$ php artisan deployer:status

Commit 68ac744
Version v1.2.13
Created by jacoblockard99
Bundled 2022-08-17 02:55 PM

You are up to date with the latest deployment.
```

This command displays available metadata for the current deployment: the timestamp, and if they exist in the bundle manifest, the commit SHA, the version string, and the initiator.

It also indicates whether the current deployment is up to date with the latest one.

### `deployer:list`

```
$ php artisan deployer:list --limit 4

+---+------------------+-------------------+---------+---------+
| # | Bundled At       | Initiated By      | Version | Commit  |
+---+------------------+-------------------+---------+---------+
| 0 | 2022-08-17 14:55 | jacoblockard99    | v1.1.13 | 68ac745 |
| 1 | 2022-08-17 14:52 | testuser          | v1.1.12 | 68ac745 |
| 2 | 2022-08-17 14:47 | testuser          | v1.1.11 | 2c9eb3c |
| 3 | 2022-08-15 18:46 | jacoblockard99    | v1.1.10 | 0c7f17a |
+---+------------------+-------------------+---------+---------+
```

This command displays a list of available bundles in the bundles directory, with the available metadata. The currently
deployed bundle (if any) is highlighted.

## Pruning
```
php artisan deployer:prune --keep 5
```

After a while, your bundles directory may become bloated with old artifact bundles. This command may be invoked to automatically delete all but the given number of most recent ones.

## Bundle Structure

An artifact bundle in the context of the `deployer` package is simply a directory. That directory contains one or more "artifacts" (which may themselves be either directories or plain files) and a `manifest.json` file.

The manifest file has the following schema (without comments, of course):

```json
{
  // The Git commit SHA for which this bundle was built, if applicable.
  "commit": "68ac745dc78a3b276350d9c22b52e907efaf85b7",
  // A string indicating the human who initated the building of this bundle, if applicable.
  "initiator": "jacoblockard99",
  // A string indicating the environment for which this bundle was built, if applicable.
  "env": "production",
  // A string indicating the application "version" for which this bundle was built, if applicable.
  "version": "v1.1.13",
  // REQUIRED. A Unix timestamp representing the time at which this bundle was built.
  "bundled_at": 1660748148,
  // A Unix timestamp representing the time at which the commit was create for which this bundle was built.
  "committed_at": 1660747824,
  // The Git ref for which this bundle was built, if applicable.
  "git_ref": "refs/tags/v1.1.13",
  // A string indicating the CI/CD "job" that built this bundle, if applicable.
  "ci_job": "production"
}
```

Only the `bundled_at` property is strictly required, but the more information the manifest contains, the more the
`deployer` commands will be able to do.

## Configuration

You may customize the directory in which `deployer` looks for artifact bundles in `config/deployer.php`:

```php
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Bundles Directory
    |--------------------------------------------------------------------------
    |
    | This value is the path (absolute or relative to the deployment root) to
    | the directory where the original bundles are stored.
    */

    'bundles_dir' => env('DEPLOYER_BUNDLES_DIR', 'storage/deployer/artifact_bundles'),
];
```

You may also specify a directory in which `deployer` may store meta-files (e.g. the `HEAD` file used to keep track of the current deployment):

```php
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Meta Directory
    |--------------------------------------------------------------------------
    |
    | This value is the path (absolute or relative to the deployment root) to
    | a directory where `deployer` can store meta-files.
    |
    | For example, a `HEAD` file will be stored in this directory containing
    | the "current" bundle.
    */

    'meta_dir' => env('DEPLOYER_META_DIR', 'storage/deployer'),
];
```

If any of these file paths begin with a `/`, then they are assumed to be absolute file paths and are used directly. If not, they will be resolved relative to the deployment root (which is the current directory by default).
