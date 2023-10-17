<?php

use App\Livewire\DhcpEntryCreate;
use App\Livewire\Index;
use Illuminate\Support\Facades\Route;

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

Route::get('/login', \App\Livewire\Login::class)->name('login');
Route::post('/authenticate', [\App\Livewire\Login::class, 'authenticate'])->name('authenticate');

Route::middleware('auth')->group(function () {
    Route::get('/', \App\Livewire\Homepage::class)->name('index');

    Route::get('/dhcp-entry/create', \App\Livewire\DhcpEntryCreate::class)->name('dhcp-entry.create');

    // Route::post('/dhcp-entry/create', [\App\Http\Controllers\DhcpEntryController::class, 'create'])->name('dhcp-entry.create');

    Route::get('/dhcp-entry/{dhcpEntry}', [\App\Http\Controllers\DhcpEntryController::class, 'show'])->name('dhcp-entry.show');
});
