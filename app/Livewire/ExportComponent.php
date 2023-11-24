<?php

namespace App\Livewire;

use App\Models\DhcpEntry;
use App\Services\ExportCsvService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportComponent extends Component
{
    public ?string $filename;
    public ?bool $exportCsvSuccess = null;
    public ?bool $exportJsonSuccess = null;
    public bool $showAlertMessage = false;

    public function mount()
    {
        $this->filename = 'dhcp-entries-' . Carbon::now()->toDateString() . '-' . Carbon::now()->toTimeString();
    }

    public function render()
    {
        return view('livewire.export-component');
    }

    public function exportCsv(): ?StreamedResponse
    {
        $dhcpData = DhcpEntry::with('notes')->get();

        if ($dhcpData->isEmpty()) {
            session()->flash('info', 'No data to export');
            $this->showAlertMessage = true;
            return null;
        }

        $dataHeaders = [
            'ID',
            'Hostname',
            'Mac Address',
            'IP Address',
            'Owner',
            'Added By',
            'SSD?',
            'Active?',
            'Imported?',
            'Created At',
            'Updated At',
            'Note (Last Updated)'
        ];

        $dataProperties = array_keys($dhcpData->first()->getAttributes());
        $dataProperties[] = 'notes';

        $response = (new ExportCsvService($dhcpData, $this->filename, $dataHeaders, $dataProperties))->exportCsvAsStream();
        if ($response->getStatusCode() !== 200) {
            session()->flash('error', 'Error exporting CSV');
            $this->showAlertMessage = true;
            return null;
        }

        $this->exportCsvSuccess = true;
        session()->flash('success', 'CSV exported successfully');
        $this->showAlertMessage = true;
        return $response;
    }

    public function exportJson(): ?StreamedResponse
    {
        $dhcpData = DhcpEntry::with('notes')->get();

        if ($dhcpData->isEmpty()) {
            session()->flash('info', 'No data to export');
            $this->showAlertMessage = true;
            return null;
        }

        $this->exportJsonSuccess = false;

        $jsonHeaders = [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $this->filename . '.json"',
        ];

        $response = response()->streamDownload(function () use ($dhcpData) {
            echo $dhcpData->toJson(JSON_PRETTY_PRINT);
            // echo json_encode($dhcpData, JSON_PRETTY_PRINT);
        }, "{$this->filename}.json", $jsonHeaders);

        if ($response->getStatusCode() !== 200) {
            session()->flash('error', 'Error exporting JSON');
            $this->showAlertMessage = true;
            return null;
        }

        $this->exportJsonSuccess = true;
        session()->flash('success', 'JSON exported successfully');
        $this->showAlertMessage = true;
        return $response;
    }
}
