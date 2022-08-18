<?php

namespace Actengage\Deployer;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * Represents a bundle.
 *
 * An entity class that represents a bundle and contains various metadata about it.
 */
final class Bundle
{
    public const MISSING_REQUIRED_ERROR = 'A required argument is missing!';

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
     * @param  ?Collection  $extra an array of extra, non-standard metadata about the bundle.
     */
    public function __construct(
        public string $path,
        public ?string $commit,
        public ?string $initiator,
        public ?string $env,
        public ?string $version,
        public Carbon $bundled_at,
        public ?Collection $extra
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

        if (! $array->has('bundled_at')) {
            throw new InvalidArgumentException(self::MISSING_REQUIRED_ERROR);
        }

        return new self(
            path: $path,
            commit: $array->get('commit'),
            initiator: $array->get('initiator'),
            env: $array->get('env'),
            version: $array->get('version'),
            bundled_at: Carbon::createFromTimestamp($array->get('bundled_at')),
            extra: static::tryCollect($array->get('extra'))
        );
    }

    protected static function tryCollect(?array $input): ?Collection
    {
        return $input ? collect($input) : null;
    }
}
