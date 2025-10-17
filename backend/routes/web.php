<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'message' => 'To-Do AI App API',
        'version' => '1.0.0',
        'status' => 'running'
    ]);
});
