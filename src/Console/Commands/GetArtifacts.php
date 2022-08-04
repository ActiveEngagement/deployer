<?php

namespace Actengage\Deployer\Console\Commands;

use Actengage\Deployer\ArtifactDeployer;
use Actengage\Deployer\BundleDeployer;
use Actengage\Deployer\BundleExtractor;
use Illuminate\Console\Command;

class GetArtifacts extends Command
{
    protected $signature = 'artifacts:get {bundle}';

    protected $description = 'Safely extracts and deploys artifacts from the given bundle.';

    public function handle(BundleExtractor $extractor, BundleDeployer $deployer): int
    {
        $extractedPath = $extractor->extract($this->argument('bundle'));

        if (! $extractedPath) {
            $this->error('Failed to extract the bundle!');
            return 1;
        }

        $deployer->deploy($extractedPath);

        return 0;
    }
}