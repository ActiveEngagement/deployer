<?php

namespace App;

use Carbon\Carbon;

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
        )
    }
}