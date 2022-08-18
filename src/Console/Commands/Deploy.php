<?php

namespace Actengage\Deployer\Console\Commands;

use Actengage\Deployer\Bundle;
use Actengage\Deployer\BundleDeployer;
use Actengage\Deployer\BundlesAccessor;
use Actengage\Deployer\CurrentBundleManager;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * A command that deploys artifact bundles.
 *
 * A custom Artisan command that deploys the artifacts in an artifact bundle to the application.
 */
final class Deploy extends Command
{
    protected $signature = 'deployer
                            {--L|latest : Deploys the latest available artifact bundle.}
                            {--C|current : Redeploys the current artifact bundle.}
                            {--c|commit= : Deploys the artifact bundle with the given Git commit SHA.}
                            {--r|bundle-version= : Deploys the artifact bundle with the given version string.}
                            {--N|number= : Deploys the artifact bundle with the given number (see deployer:list).}';

    protected $description = 'Deploys an artifact bundle.';

    public function handle(
        BundlesAccessor $bundles,
        CurrentBundleManager $currentBundle,
        BundleDeployer $deployer,
    ): int {
        $this->setup();

        $bundle = $this->getBundle($bundles->all(), $currentBundle);

        if (! $bundle) {
            return 1;
        }

        $deployer->deploy($bundle->path);

        $this->info('Bundle deployed!');
        $this->newLine();

        $this->call('deployer:status');

        return 0;
    }

    private function getBundle(Collection $bundles, CurrentBundleManager $currentBundle): ?Bundle
    {
        $bundle = null;

        $number = $this->option('number');
        $version = $this->option('bundle-version');
        $commit = $this->option('commit');

        if ($this->option('latest')) {
            $bundle = $bundles->first();

            if (is_null($bundle)) {
                $this->error('No latest bundle found.');
            }
        } elseif ($this->option('current')) {
            $bundle = $bundles->first(fn ($b) => $currentBundle->is($b));

            if (is_null($bundle)) {
                $this->warnHeadBroken();
                $this->error('No current bundle found.');
            }
        } elseif ($number) {
            $bundle = $bundles->get($number);

            if (is_null($bundle)) {
                $this->error("No bundle with number $number found.");
            }
        } elseif ($version) {
            $bundle = $bundles->first(fn ($b) => $b->version === $version);

            if (is_null($bundle)) {
                $this->error("No bundle with version $version found.");
            }
        } elseif ($commit) {
            $matches = $bundles->filter(fn ($b) => Str::startsWith($b->commit, $commit));

            if ($matches->count() === 1) {
                $bundle = $matches->first();
            } elseif ($matches->count() === 0) {
                $this->error("No bundles with commit $commit found.");
            } else {
                $this->error('Ambiguous commit SHA!');
            }
        } else {
            $this->error('The bundle to deploy must be given with one of the following options: --latest, --commit, or --bundle-version.');
        }

        return $bundle;
    }
}
