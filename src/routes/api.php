<?php

use Illuminate\Support\Facades\Route;
use Rboschin\LaravelIpAccess\Controllers\IpAccessWhiteController;
use Rboschin\LaravelIpAccess\Controllers\IpAccessBlackController;

Route::prefix('api/ip-access')->group(function () {
    // Whitelist routes
    Route::get('/whitelist', [IpAccessWhiteController::class, 'index']);
    Route::post('/whitelist', [IpAccessWhiteController::class, 'store']);
    Route::put('/whitelist/{ipWhite}', [IpAccessWhiteController::class, 'update']);
    Route::delete('/whitelist/{ipWhite}', [IpAccessWhiteController::class, 'destroy']);

    // Blacklist routes
    Route::get('/blacklist', [IpAccessBlackController::class, 'index']);
    Route::post('/blacklist', [IpAccessBlackController::class, 'store']);
    Route::put('/blacklist/{ipBlack}', [IpAccessBlackController::class, 'update']);
    Route::delete('/blacklist/{ipBlack}', [IpAccessBlackController::class, 'destroy']);
});
