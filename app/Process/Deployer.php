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

    public function getRepositoryName(array $payload): string|bool
    {
        $repository = Arr::get($payload, 'repository.name');
        if (! $repository) {
            Log::notice('- No repository ');
            $this->fail('No repository');

            return false;
        }

        return $repository;
    }

    public function getDeployConfig(string $repository): array|bool
    {
        $configs = Storage::json('deploy.json');
        if (is_null($configs)) {
            Log::notice('- No deploy.json');
            $this->fail('No deploy.json');

            return false;
        }
        $config = Arr::first($configs, function (array $value) use ($repository) {
            return Arr::get($value, 'repository') == $repository;
        });
        if (is_null($config)) {
            Log::notice('- No config found');
            $this->fail('No config found');

            return false;
        }

        return $config;
    }

    public function executeConfig(array $config)
    {
        $path = Arr::get($config, 'path');
        if (is_null($path)) {
            Log::notice('- No "path" in config');
            $this->fail('No "path" in config');

            return false;
        }
        $command = Arr::get($config, 'command');

        $result = Process::path($path)->run($command);
        if ($result->failed()) {
            Log::error('- Failed '.$result->errorOutput());
            $this->fail('Failed whith message '.$result->errorOutput());
        }
    }

    public function handle()
    {
        $id = $this->webhookCall->id;
        Log::notice('Deployer (id : {id}) - Start Process ', ['id' => $id]);

        $payload = $this->webhookCall->payload;
        $repository = $this->getRepositoryName($payload);
        if ($repository === false) {
            return;
        }

        $config = $this->getDeployConfig($repository);
        if ($config === false) {
            return;
        }

        $this->executeConfig($config);
        Log::notice('Deployer (id : {id}) - Process finish', ['id' => $id]);
    }
}
