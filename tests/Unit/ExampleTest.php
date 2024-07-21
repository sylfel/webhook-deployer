<?php

namespace Tests\Unit;

use App\Process\Deployer;
use Spatie\WebhookClient\Models\WebhookCall;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_deployer_read_json(): void
    {
        $hook = new WebhookCall();
        $deployer = new Deployer($hook);
        $config = $deployer->getDeployConfig('test1');

        $this->assertEquals('~/', $config['path']);
    }
}
