<?php

namespace App\Process;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Process;
use Spatie\WebhookClient\Jobs\ProcessWebhookJob as SpatieProcessWebhookJob;

class Deployer extends SpatieProcessWebhookJob
{
    public function handle()
    {
        $payload = $this->webhookCall->payload;
        $repository = Arr::get($payload, 'repository.name');
        if (!$repository) {
            return;
        }
        $pathSites = config('app.home');
        $homeDir = $pathSites . $repository;
        if (!file_exists($homeDir)) {
            return;
        }
        $scriptPath = $homeDir . '/.scripts/deploy.sh';
        if (!file_exists($scriptPath)) {
            return;
        }
        Process::run($scriptPath);
    }
}
