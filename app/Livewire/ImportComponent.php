<?php

namespace App\Livewire;

use App\Jobs\ImportDhcpEntriesJob;
use Illuminate\Http\Request;
use Livewire\Attributes\Validate;
use Livewire\Component;
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

    public function import(Request $request): void
    {
        $this->validate();

//        ImportDhcpEntriesJob::dispatch($this->uploadedCsv->getRealPath());

        $this->importSuccess = true;
        session()->flash('success', 'Data imported successfully');
        $this->showAlertMessage = true;
    }
}
