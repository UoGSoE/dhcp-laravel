<?php

use App\Http\Controllers\ExportController;
use App\Livewire\DhcpEntryCreate;
use App\Livewire\DhcpEntryTable;
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
    Route::get('/', \App\Livewire\DhcpEntryTable::class)->name('index');
    Route::get('/dhcp-entries', \App\Livewire\DhcpEntryTable::class)->name('dhcp-entries');
    Route::get('/dhcp-entry/create', \App\Livewire\DhcpEntryCreate::class)->name('dhcp-entry.create');
    Route::get('/dhcp-entry/{dhcpEntry}/edit', \App\Livewire\DhcpEntryEdit::class)->name('dhcp-entry.edit');
    Route::get('/dhcp-entry/config', \App\Livewire\DhcpConfigForm::class)->name('dhcp-config');
    Route::get('/logout', [\App\Http\Controllers\LogoutController::class, 'logout'])->name('logout');
    Route::get('/export-csv', [\App\Http\Controllers\ExportController::class, 'exportCsv'])->name('export-csv');
    Route::get('/export-json', [\App\Http\Controllers\ExportController::class, 'exportJson'])->name('export-json');
    Route::get('/import-csv', \App\Livewire\ImportComponent::class)->name('import-csv.index');
    Route::post('/import-csv/upload', [\App\Livewire\ImportComponent::class, 'import'])->name('import-csv.upload');
});
