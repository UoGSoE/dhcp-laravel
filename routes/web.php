<?php

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

Route::get('/', function () {
    return view('welcome');
});

Route::post('/dhcp-entry/create', [\App\Http\Controllers\DhcpEntryController::class, 'create'])->name('dhcp-entry.create');

Route::get('/dhcp-entry/{dhcpEntry}', [\App\Http\Controllers\DhcpEntryController::class, 'show'])->name('dhcp-entry.show');

Route::get('login', [\App\Http\Controllers\LoginController::class, 'login'])->name('login');

Route::middleware('auth')->group(function () {
    Route::get('/', [HomepageController::class, 'index'])->name('index');
});
