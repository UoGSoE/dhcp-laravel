<?php

namespace App\Http\Controllers;

use App\Jobs\ImportDhcpEntriesJob;
use App\Jobs\ImportDhcpEntriesJobTest;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    public function render()
    {
        return view('livewire.import-component-test');
    }

    public function import(Request $request): void
    {
        $request->validate([
            'upload' => 'required|mimes:csv'
        ]);
        $filePath = $request->file('upload')->getRealPath();
        $stream = fopen($filePath, 'r');

        $data = [];
        while(($row = fgetcsv($stream)) !== false) {
            $data[] = $row;
        }
        fclose($stream);

        // Remove header element from dhcp data if exists
        if ($data[0][0] == "ID") {
            array_shift($data);
        }

        ImportDhcpEntriesJob::dispatch($data, $request->user()->email);

        unlink($filePath);
    }
}
