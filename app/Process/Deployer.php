<?php

namespace App\Process;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Spatie\WebhookClient\Jobs\ProcessWebhookJob as SpatieProcessWebhookJob;

class Deployer extends SpatieProcessWebhookJob
{
    public $tries = 1;

    public function handle()
    {
        $payload = $this->webhookCall->payload;
        $id = $this->webhookCall->id;
        $repository = Arr::get($payload, 'repository.name');
        if (! $repository) {
            Log::notice('Deployer (id : {id}) - no repository ', ['id' => $id]);

            return;
        }

        $pathSites = config('app.home');
        $homePath = $pathSites.$repository;
        if (! file_exists($homePath)) {
            Log::notice('Deployer (id : {id}) - Path not exists {path}', ['id' => $id, 'path' => $homePath]);

            return;
        }

        $scriptPath = $homePath.'/.script/deploy.sh';
        if (! file_exists($scriptPath)) {
            Log::notice('Deployer (id : {id}) - Script not exists {path}', ['id' => $id, 'path' => $scriptPath]);

            return;
        }
        Log::notice('Deployer (id : {id}) - Run {path}', ['id' => $id, 'path' => $scriptPath]);
        $result = Process::path($homePath)->run(['/bin/sh', $scriptPath]);

        $result = Process::path($homePath)->run('bash -lc '.$scriptPath);
        if ($result->failed()) {
            Log::error('Deployer failed '.$result->errorOutput());
            $this->fail('Deployer '.$id.' Failed whith message '.$result->errorOutput());

            return;
        }
        Log::notice("Deployer ({$id}) success");
    }
}
