<?php

namespace App\Http\Controllers;

use App\Models\DhcpEntry;
use App\Services\ExportCsvService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public Collection $dhcpData;
    public ?string $filename;

    public function __construct()
    {
        $this->dhcpData = DhcpEntry::with('notes')->get();
        $this->filename = 'dhcp-entries-' . Carbon::now()->toDateString() . '-' . Carbon::now()->toTimeString();
    }

    public function exportCsv(): StreamedResponse
    {

        if ($this->dhcpData->isEmpty()) {
            throw new \Exception('No data to export');
            //todo
        }

        $dataHeaders = [
            'ID',
            'Hostname',
            'Mac Address',
            'IP Address',
            'Owner',
            'Added By',
            'Campus System?',
            'Active?',
            'Imported?',
            'Created At',
            'Updated At',
            'Note (Last Updated)'
        ];

        $dataProperties = array_keys($this->dhcpData->first()->getAttributes());
        $dataProperties[] = 'notes';

        return (new ExportCsvService($this->dhcpData, $this->filename, $dataHeaders, $dataProperties))->exportCsvAsStream();
    }

    public function exportJson(): JsonResponse
    {
        $jsonHeaders = [
            'Content-Type: application/json; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $this->filename . '.json"',
        ];

        return response()->json($this->dhcpData, 200, $jsonHeaders);
    }
}
