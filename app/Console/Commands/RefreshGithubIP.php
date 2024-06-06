<?php

namespace App\Console\Commands;

use App\Models\IpRange;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use IPLib\Factory;

class RefreshGithubIP extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:refresh-github-ip';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Refresh github's action ip list";

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $response = Http::get('https://api.github.com/meta');
        if ($response->successful()) {
            IpRange::truncate();
            $starting_time = microtime(true);
            $actionIps = collect($response->json()['hooks']);
            $ipRangeList = $actionIps->map(function (string $item) {
                $range = Factory::parseRangeString($item);

                return new IpRange([
                    'adresseType' => $range->getAddressType(),
                    'rangeFrom' => $range->getComparableStartString(),
                    'rangeTo' => $range->getComparableEndString(),
                ]);
            });
            IpRange::insert($ipRangeList->toArray());
            $finished_time = microtime(true);
            $time = ($finished_time - $starting_time); // time in sec
            $this->comment("Process {$actionIps->count()} IP range in $time ");
        }
    }
}
