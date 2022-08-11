<?php

namespace Actengage\Deployer;

use Carbon\Carbon;
use Illuminate\Support\Str;

/**
 * Represents a bundle.
 *
 * An entity class that represents a bundle and contains various metadata about it.
 */
final class Bundle
{
    /**
     * Creates a new instance of Bundle.
     *
     * Creates a new instance of `Bundle` with the given metadata.
     *
     * @param  string  $path the full file path to the bundle on disk.
     * @param  ?string  $commit the full SHA of the Git commit for which this bundle was built.
     * @param  ?string  $initiator a string representation of the person who triggered the building of the bundle.
     * @param  ?string  $env the environment (e.g. `"testing"` or `"production"`) for which this bundle was built.
     * @param  ?string  $version the release version for which this bundle was built. This likely will only apply to
     * production builds.
     * @param  Carbon  $bundled_at the datetime at which this bundle was built.
     * @param  ?Carbon  $committed_at the datetime at which the Git commit for which this bundle was built was committed.
     * @param  ?string  $git_ref a Git reference (e.g. `"refs/heads/master"`) associated with the bundle.
     * @param  ?string  $ci_job if the bundle was built in a CI environment, the name of the CI job or runner that built
     * the bundle.
     */
    public function __construct(
        public string $path,
        public ?string $commit,
        public ?string $initiator,
        public ?string $env,
        public ?string $version,
        public Carbon $bundled_at,
        public ?Carbon $committed_at,
        public ?string $git_ref,
        public ?string $ci_job
    ) {
    }

    /**
     * Gets a short commit SHA.
     *
     * Gets a truncated-to-7-characters version of the Git commit SHA.
     *
     * @return string
     */
    public function shortCommit(): string
    {
        return Str::substr($this->commit, 0, 7);
    }

    /**
     * Gets the file name.
     *
     * Gets the name of the bundle directory on disk by parsing its path on disk.
     *
     * @return string
     */
    public function fileName(): string
    {
        return basename($this->path);
    }

    /**
     * Creates a new instance of Bundle by parsing raw JSON.
     *
     * Creates a new instance of `Bundle` for the given bundle path by parsing the given JSON and reading the metadata
     * from it.
     *
     * @param  string  $path the full file path to the bundle on disk.
     * @param  string  $json the raw JSON from which to read the bundle metadata.
     * @return self the created `Bundle` instance.
     */
    public static function fromJson(string $path, string $json): self
    {
        $array = collect(json_decode($json, true));

        return new self(
            path: $path,
            commit: $array->get('commit'),
            initiator: $array->get('initiator'),
            env: $array->get('env'),
            version: $array->get('version'),
            bundled_at: Carbon::createFromTimestamp($array->get('bundled_at')),
            committed_at: Carbon::createFromTimestamp($array->get('committed_at')),
            git_ref: $array->get('git_ref'),
            ci_job: $array->get('ci_job'),
        );
    }
}
