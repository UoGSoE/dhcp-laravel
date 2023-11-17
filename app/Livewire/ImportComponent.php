<?php

namespace App\Livewire;

use App\Models\DhcpEntry;
use App\Models\Note;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Livewire\Component;
use DateTime;

class ImportComponent extends Component
{
    public function render()
    {
        return view('livewire.import-component');
    }

    public function import(Request $request)
    {
        if (!$request->hasFile('upload')) {
            //todo
            return redirect()->back()->with('error', 'No file selected');
        }

        if (!$request->file('upload')->isValid()) {
            //todo
            return redirect()->back()->with('error', 'File is not valid');
        }

        $file = $request->file('upload');
        if (!$file instanceof UploadedFile || $file->getMimeType() !== 'text/csv') {
            return;
        }

        $stream = fopen($file->getRealPath(), 'r');

        $data = [];
        while(($row = fgetcsv($stream)) !== false) {
            $data[] = $row;
        }
        fclose($stream);

        // Remove header element from dhcp data
        array_shift($data);

        $dhcpEntries = [];
        $notes = [];

        foreach ($data as $entry) {
            $dhcpEntries[] = [
                'id' => $entry[0],
                'mac_address' => $entry[1],
                'hostname' => $entry[2],
                'ip_address' => $entry[3] ? $entry[3] : null,
                'owner' => $entry[4],
                'added_by' => $entry[5],
                'is_ssd' => $entry[6] === "TRUE",
                'is_active' => $entry[7] === "TRUE",
                'is_imported' => true,
                'created_at' => (new DateTime)->createFromFormat('d/m/Y H:i', $entry[9]),
                'updated_at' => (new DateTime)->createFromFormat('d/m/Y H:i', $entry[10]),
            ];

            if ($entry[11] !== "") {
                $note = json_decode($entry[11], true);
                $note['created_at'] = (new DateTime)->setTimestamp(strtotime($note['created_at']));
                $note['updated_at'] = (new DateTime)->setTimestamp(strtotime($note['updated_at']));
                $notes[] = $note;
            }
        }

        try {
            DhcpEntry::insert($dhcpEntries);
            Note::insert($notes);
        } catch (\Exception $e) {
            redirect()->back()->with('error', 'Error importing data');
        }

        return redirect()->back()->with('success', 'Data imported successfully');
    }
}
