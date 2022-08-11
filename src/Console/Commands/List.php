<?php

namespace Actengage\Deployer\Console\Commands;

use App\Bundle;
use App\Contracts\BundlesRepository;
use Illuminate\Console\Command;

final class BundlesList extends Command
{
    protected $signature = 'deployer:list {--limit=10}';

    protected $description = '';

    public function handle(BundlesRepository $bundles): int
    {
        $headers = ['Bundled At', 'Version', 'Commit'];
        $rows = [];

        $bundles->all(limit: (int) $this->argument('limit'))->each(function (Bundle $bundle) use ($rows) {
            $rows [] = [
                $bundle->bundled_at->toDateTimeString(),
                $bundle->version,
                $bundle->commit
            ];
        });

        $this->table($headers, $rows);

        return 0;
    }
}
