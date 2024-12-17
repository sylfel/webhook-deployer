<?php

use App\Http\Middleware\WhiteIpList;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::webhooks('/deployer')->middleware(WhiteIpList::class);
