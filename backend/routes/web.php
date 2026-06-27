<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'api'     => 'L.A. Couture API',
        'version' => '1.0',
        'status'  => 'running',
        'docs'    => '/api',
    ]);
});
