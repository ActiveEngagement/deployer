<?php

namespace Actengage\Deployer;

use Carbon\Carbon;
use Illuminate\Support\Str;

final class Bundle
{
    public function __construct
    (
        public string $commit,
        public string $initiator,
        public string $env,
        public string $version,
        public Carbon $bundled_at,
        public Carbon $committed_at,
        public string $git_ref,
        public string $ci_job
    )
    {
    }

    public function shortCommit(): string
    {
        return Str::substr($this->commit, 0, 7);
    }

    public static function fromJson(string $json): self
    {
        $array = json_decode($json, true);

        return new self(
            commit: $array['commit'],
            initiator: $array['initiator'],
            env: $array['env'],
            version: $array['version'],
            bundled_at: Carbon::createFromTimestamp($array['bundled_at']),
            committed_at: Carbon::createFromTimestamp($array['committed_at']),
            git_ref: $array['git_ref'],
            ci_job: $array['ci_job'],
        );
    }
}