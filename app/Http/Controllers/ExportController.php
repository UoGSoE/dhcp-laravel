<?php

namespace App\Http\Controllers;

use App\Models\Host;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ExportController extends Controller
{
    public function csv(): Response
    {
        $hosts = Host::orderBy('hostname')->get();

        $header = implode(',', ['id', 'Hostname', 'MAC', 'Status', 'IP', 'Owner', 'SSD']);

        $rows = $hosts->map(fn (Host $host) => implode(',', [
            $host->id,
            $host->hostname,
            $host->mac,
            $host->status->value,
            $host->ip ?? '',
            $host->owner,
            $host->ssd,
        ]));

        $csv = $header."\n".$rows->implode("\n");

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename=dhcp-hosts.csv');
    }

    public function json(): JsonResponse
    {
        $hosts = Host::orderBy('hostname')->get();

        return response()->json([
            'Message' => 'OK',
            'Status' => 1,
            'Data' => $hosts,
        ]);
    }
}
