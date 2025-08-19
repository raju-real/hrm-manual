<?php

use Illuminate\Support\Facades\Route;

Route::view('login','admin.auth.admin_login')->name('login');
Route::middleware('auth')->group(function () {
    //
});

