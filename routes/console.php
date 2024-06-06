<?php

use App\Console\Commands\RefreshGithubIP;
use Illuminate\Queue\Console\WorkCommand;
use Illuminate\Support\Facades\Schedule;

Schedule::command(RefreshGithubIP::class)->daily();
Schedule::command(WorkCommand::class, ['--stop-when-empty'])->everyMinute();
