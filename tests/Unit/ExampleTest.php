<?php

namespace Tests\Unit;

use App\Process\Deployer;
use Spatie\WebhookClient\Models\WebhookCall;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_find_config(): void
    {
        $hook = new WebhookCall;
        $deployer = new Deployer($hook);
        $configs = [[
            'conditions' => [
                'headers.X-GitHub-Event' => 'push',
                'payload.ref' => 'refs/heads/main',
                'payload.repository.full_name' => 'sylfel/webhook-deployer',
            ],
        ]];
        $data = [
            'headers' => [
                'X-GitHub-Event' => ['push'],
            ],
            'payload' => [
                'ref' => 'refs/heads/main',
                'repository' => [
                    'full_name' => 'sylfel/webhook-deployer',
                ],
            ],
        ];
        $config = $deployer->findConfig($configs, $data);
        $this->assertNotEmpty($config);
    }

    public function test_find_config_data_lowercase(): void
    {
        $hook = new WebhookCall;
        $deployer = new Deployer($hook);
        $configs = [[
            'conditions' => [
                'headers.X-GitHub-Event' => 'push',
                'payload.ref' => 'refs/heads/main',
                'payload.repository.full_name' => 'sylfel/webhook-deployer',
            ],
        ]];
        $data = [
            'headers' => [
                'x-github-event' => ['push'],
            ],
            'payload' => [
                'ref' => 'refs/heads/main',
                'repository' => [
                    'full_name' => 'sylfel/webhook-deployer',
                ],
            ],
        ];
        $config = $deployer->findConfig($configs, $data);
        $this->assertNotEmpty($config);
    }

    public function test_not_find_config(): void
    {
        $hook = new WebhookCall;
        $deployer = new Deployer($hook);
        $configs = [[
            'conditions' => [
                'headers.X-GitHub-Event' => ['push'],
                'payload.ref' => 'refs/heads/main',
                'payload.repository.full_name' => 'nothing',
            ],
        ]];
        $data = [
            'headers' => [
                'X-GitHub-Event' => 'push',
            ],
            'payload' => [
                'ref' => 'refs/heads/main',
                'repository' => [
                    'full_name' => 'sylfel/webhook-deployer',
                ],
            ],
        ];
        $config = $deployer->findConfig($configs, $data);
        $this->assertNull($config);
    }
}
