<?php

use App\Livewire\HostForm;
use App\Livewire\HostList;
use Illuminate\Support\Facades\Route;

require __DIR__.'/sso-auth.php';

Route::middleware('auth')->group(function () {
    Route::get('/', HostList::class)->name('home');
    Route::get('/hosts/create', HostForm::class)->name('hosts.create');
    Route::get('/hosts/{host}/edit', HostForm::class)->name('hosts.edit');
});
