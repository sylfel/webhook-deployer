<?php

namespace App\Process;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Spatie\WebhookClient\Jobs\ProcessWebhookJob as SpatieProcessWebhookJob;

class Deployer extends SpatieProcessWebhookJob
{
    public $tries = 1;

    public function executeConfig(array $config)
    {
        $path = Arr::get($config, 'path');
        if (is_null($path)) {
            Log::notice('- No "path" in config');
            $this->fail('No "path" in config');

            return false;
        }
        $command = Arr::get($config, 'command');
        if (is_null($command)) {
            Log::notice('- No "command" in config');
            $this->fail('No "command" in config');

            return false;
        }

        $result = Process::path($path)->run($command);
        if ($result->failed()) {
            Log::error('- Failed '.$result->errorOutput());
            $this->fail('Failed whith message '.$result->errorOutput());
        }
    }

    public function loadConfigs(): bool|array
    {
        $configs = Storage::json('deploy.json');
        if (is_null($configs)) {
            Log::notice('- No deploy.json');
            $this->fail('No deploy.json');

            return false;
        }

        return $configs;
    }

    public function findConfig(array $configs, array $data): ?array
    {
        return Arr::first($configs, function (array $config) use ($data) {
            $conditions = Arr::get($config, 'conditions');
            if (! $conditions) {
                return false;
            }

            return collect($conditions)->every(function ($value, $key) use ($data) {
                return Arr::get($data, $key) == $value;
            });
        });
    }

    public function handle(): void
    {
        $id = $this->webhookCall->id;
        Log::notice('Deployer (id : {id}) - Start Process ', ['id' => $id]);

        $configs = $this->loadConfigs();
        if ($configs === false) {
            return;
        }
        $payload = $this->webhookCall->payload;
        $headers = $this->webhookCall->headers;

        $config = $this->findConfig($configs, ['payload' => $payload, 'headers' => $headers]);
        if (is_null($config)) {
            Log::notice('- No config found');
        } else {
            $this->executeConfig($config);
        }
        Log::notice('Deployer (id : {id}) - Process finish', ['id' => $id]);
    }
}
