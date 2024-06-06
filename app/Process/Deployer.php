<?php

namespace App\Process;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Spatie\WebhookClient\Jobs\ProcessWebhookJob as SpatieProcessWebhookJob;

class Deployer extends SpatieProcessWebhookJob
{
    public function handle()
    {
        $payload = $this->webhookCall->payload;
        $repository = Arr::get($payload, 'repository.name');
        if (! $repository) {
            Log::notice('Deployer (id : {id}) - no repository ', ['id' => $this->webhookCall->id]);

            return;
        }
        $pathSites = config('app.home');
        $homePath = $pathSites.$repository;
        if (! file_exists($homePath)) {
            Log::notice('Deployer (id : {id}) - Path not exists {path}', ['id' => $this->webhookCall->id, 'path' => $homePath]);

            return;
        }
        $scriptPath = $homePath.'/.script/deploy.sh';
        if (! file_exists($scriptPath)) {
            Log::notice('Deployer (id : {id}) - Script not exists {path}', ['id' => $this->webhookCall->id, 'path' => $scriptPath]);

            return;
        }
        Log::notice('Deployer (id : {id}) - Run {path}', ['id' => $this->webhookCall->id, 'path' => $scriptPath]);
        Process::run($scriptPath);
    }
}
