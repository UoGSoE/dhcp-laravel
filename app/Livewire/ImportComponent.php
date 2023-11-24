<?php

namespace App\Livewire;

use Livewire\Attributes\Validate;
use App\Models\DhcpEntry;
use App\Models\Note;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Livewire\Component;
use DateTime;
use Livewire\WithFileUploads;

class ImportComponent extends Component
{
    use WithFileUploads;

    public bool $showAlertMessage = false;
    public ?bool $importSuccess = null;

    #[Validate('required|mimes:csv,xls,xls')]
    public $uploadedCsv;

    public function render()
    {
        return view('livewire.import-component');
    }

    public function import(Request $request)
    {
        $this->validate();

        $stream = fopen($this->uploadedCsv->getRealPath(), 'r');

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
                'is_ssd' => strtolower($entry[6]) === "true",
                'is_active' => strtolower($entry[7]) === "true",
                'is_imported' => true,
                'created_at' => (new DateTime)->setTimestamp(strtotime($entry[9])),
                'updated_at' => (new DateTime)->setTimestamp(strtotime($entry[10])),
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
            $this->importSuccess = false;
            session()->flash('error', "Error importing data: {$e->getMessage()}");
            $this->showAlertMessage = true;
            return;
        }

        $this->importSuccess = true;
        session()->flash('success', 'Data imported successfully');
        $this->showAlertMessage = true;
    }
}
