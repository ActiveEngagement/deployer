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
    /*
    |--------------------------------------------------------------------------
    | Artifact Rules
    |--------------------------------------------------------------------------
    |
    | This value is an associative array of artifact source files (relative to
    | the bundle root) and destination paths (relative to the deployment root).
    |
    | The default configuration below expects a single directory called "build"
    | in the bundle, which will be deployed to "{PROJECT_ROOT}/public/build".
    |
    | Note that nested source paths are not permitted (i.e.
    | `'public/build' => 'public/build'`). Each artifact **must** have its own,
    | top-level file or directory in the bundle.
    |
    | You may add as many rules as you wish.
    */

    'artifacts' => [
        'build' => 'public/build',
    ],
];
