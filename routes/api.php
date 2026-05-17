<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    require __DIR__.'/UserRoute.php';
    require __DIR__.'/TaskRoute.php';

});
