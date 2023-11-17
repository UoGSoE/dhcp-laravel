<?php

namespace App\Http\Controllers;

use App\Models\DhcpEntry;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public ?string $filename;
    public $dhcpData;
    public const DATA_HEADERS = [
        'ID',
        'Mac Address',
        'Hostname',
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

    public function __construct()
    {
        $this->dhcpData = DhcpEntry::with('notes')->get();
        $this->filename = 'dhcp-entries-' . Carbon::now()->toDateString() . '-' . Carbon::now()->toTimeString();
    }

    public function exportCsv(): StreamedResponse
    {
        $csvHeaders = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $this->filename . '.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, self::DATA_HEADERS);

            foreach($this->dhcpData as $dhcpEntry) {
                fputcsv($file, [
                        $dhcpEntry->id,
                        $dhcpEntry->mac_address,
                        $dhcpEntry->hostname,
                        $dhcpEntry->ip_address,
                        $dhcpEntry->owner,
                        $dhcpEntry->added_by,
                        $dhcpEntry->is_ssd ? 'True' : 'False',
                        $dhcpEntry->is_active ? 'True' : 'False',
                        $dhcpEntry->is_imported ? 'True' : 'False',
                        $dhcpEntry->created_at,
                        $dhcpEntry->updated_at,
                        $dhcpEntry->notes->all() ? json_encode($dhcpEntry->notes->sortByDesc('updated_at')->first(), JSON_PRETTY_PRINT) : ''
                    ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $csvHeaders);
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
