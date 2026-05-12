<?php

use Illuminate\Support\Facades\Route;

require __DIR__.'/UserRoute.php';
require __DIR__.'/TaskRoute.php';

Route::get('/api/v1', function () {
    return view('welcome');
});
