<?php

use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Livewire::setUpdateRoute(function ($handle) {
    return Route::post('qsync-backend/public/livewire/update', $handle);
});

Livewire::setScriptRoute(function ($handle) {
    return Route::get('qsync-backend/vendor/livewire/livewire/dist/livewire.js', $handle);
});
