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
    // TODO 'index' should be 'home' by local convention
    Route::get('/', \App\Livewire\DhcpEntryTable::class)->name('index');
    Route::get('/dhcp-entries', \App\Livewire\DhcpEntryTable::class)->name('dhcp-entries');
    Route::get('/dhcp-entry/create', \App\Livewire\DhcpEntryCreate::class)->name('dhcp-entry.create');
    Route::get('/dhcp-entry/{dhcpEntry}/edit', \App\Livewire\DhcpEntryEdit::class)->name('dhcp-entry.edit');
    Route::get('/dhcp-entry/config', \App\Livewire\DhcpConfigForm::class)->name('dhcp-config');
    Route::post('/logout', [\App\Http\Controllers\LogoutController::class, 'logout'])->name('logout');
    Route::get('/export', \App\Livewire\ExportComponent::class)->name('export.index');
    Route::get('/import-csv', [\App\Http\Controllers\ImportController::class, 'render'])->name('import-csv.index');
    Route::post('/import-test', [\App\Http\Controllers\ImportController::class, 'import'])->name('import');
    Route::get('/documentation', \App\Livewire\DocumentationComponent::class)->name('documentation');
});

Route::get('/dhcp-cache', [\App\Http\Controllers\DhcpCacheController::class, 'show'])->name('dhcp-cache.show');
