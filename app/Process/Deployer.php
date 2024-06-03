<?php

namespace App\Process;

use Illuminate\Support\Facades\Process;
use Spatie\WebhookClient\Jobs\ProcessWebhookJob as SpatieProcessWebhookJob;

class Deployer extends SpatieProcessWebhookJob
{
    public function handle()
    {
        $repository = $this->webhookCall->payload['repository'];
        $pathSites = config('app.home');
        $homeDir = $pathSites . $repository;
        if (file_exists($homeDir)) {
            Process::run($homeDir . '/.scripts/deploy.sh');
        }
    }
}
