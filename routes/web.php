<?php

use App\Http\Controllers\ExportController;
use App\Livewire\DhcpSectionEditor;
use App\Livewire\HostForm;
use App\Livewire\HostList;
use Illuminate\Support\Facades\Route;

require __DIR__.'/sso-auth.php';

Route::middleware('auth')->group(function () {
    Route::get('/', HostList::class)->name('home');
    Route::get('/hosts/create', HostForm::class)->name('hosts.create');
    Route::get('/hosts/{host}/edit', HostForm::class)->name('hosts.edit');
    Route::get('/subnet-usage', fn () => view('pages.subnet-usage'))->name('subnet-usage');
    Route::get('/export/csv', [ExportController::class, 'csv'])->name('export.csv');
    Route::get('/export/json', [ExportController::class, 'json'])->name('export.json');

    Route::middleware('can:dhcp-admin')->group(function () {
        Route::get('/dhcp-sections/{sectionName}/edit', DhcpSectionEditor::class)->name('dhcp-sections.edit');
    });
});
