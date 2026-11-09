<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| COMPRO ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/compro', function () {
    return view('compro.index');
});