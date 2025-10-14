<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CustomerConfigController;

Route::middleware('api')->get('/ping', function () {
    return response()->json(['status' => 'ok']);
});

Route::middleware('api')->get('/customer-config', [CustomerConfigController::class, 'getConfig']);
