<?php

use App\Console\Commands\RefreshGithubIP;
use Illuminate\Queue\Console\WorkCommand;
use Illuminate\Support\Facades\Schedule;
use Spatie\WebhookClient\Models\WebhookCall;

Schedule::command(RefreshGithubIP::class)->daily();
Schedule::command(WorkCommand::class, ['--stop-when-empty'])->everyMinute();
Schedule::command('model:prune', [
    '--model' => [WebhookCall::class],
])->daily();
