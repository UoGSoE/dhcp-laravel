<?php

use App\Http\Controllers\Api\DhcpApiController;
use App\Http\Controllers\Api\HostStatusController;
use Illuminate\Support\Facades\Route;

Route::prefix('dhcp')->group(function () {
    Route::get('check-updates', [DhcpApiController::class, 'checkUpdates']);
    Route::get('config', [DhcpApiController::class, 'config']);
    Route::get('hosts', [DhcpApiController::class, 'hosts']);
    Route::match(['get', 'post'], 'flag-error', [DhcpApiController::class, 'flagError']);
    Route::post('hosts/{host}/online', [HostStatusController::class, 'online']);
    Route::post('hosts/{host}/offline', [HostStatusController::class, 'offline']);
});
