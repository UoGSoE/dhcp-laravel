<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\DhcpFileCorrupt;
use App\Models\Checkin;
use App\Models\Host;
use App\Services\DhcpConfigGenerator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;

class DhcpApiController extends Controller
{
    public function checkUpdates(Request $request): Response
    {
        $hostname = $request->query('host', '');

        $lastChange = Host::max('last_updated');
        $lastCheckin = Checkin::where('hostname', $hostname)->max('checked_in_at');

        $needsUpdate = ! $lastCheckin || $lastChange > $lastCheckin;

        Checkin::create([
            'hostname' => $hostname,
            'checked_in_at' => now(),
        ]);

        return response($needsUpdate ? 'Yes' : 'No')
            ->header('Content-Type', 'text/plain');
    }

    public function config(DhcpConfigGenerator $generator): Response
    {
        return response($generator->generate())
            ->header('Content-Type', 'text/plain');
    }

    public function hosts(Request $request): Response
    {
        $hostname = $request->query('host');

        if ($hostname) {
            Checkin::create([
                'hostname' => $hostname,
                'checked_in_at' => now(),
            ]);
        }

        $csv = Host::orderBy('hostname')
            ->get()
            ->map(fn (Host $host) => implode(',', [
                $host->id,
                $host->hostname,
                $host->mac,
                $host->status->value,
                $host->ip ?? '',
                $host->owner,
                $host->ssd,
            ]))
            ->implode("\n");

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename=dhcpinfo.csv');
    }

    public function flagError(): Response
    {
        Mail::send(new DhcpFileCorrupt);

        return response('OK');
    }
}
