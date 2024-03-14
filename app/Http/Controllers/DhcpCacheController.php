<?php

namespace App\Http\Controllers;

use App\Models\DhcpConfig;
use App\Models\DhcpEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DhcpCacheController extends Controller
{
    public function show()
    {
        $dhcpConfigModel = DhcpConfig::first();
        $dhcpEntryModels = DhcpEntry::all();
        $entries = [];

        foreach ($dhcpEntryModels as $entry) {
            $entries[] = $entry->getDhcpFileFormat();
        }

        return Cache::rememberForever('dhcpFile', function () use ($dhcpConfigModel, $entries) {
            return view('file.dhcp-file', [
                'header' => $dhcpConfigModel->header . PHP_EOL,
                'entries' => implode(PHP_EOL, $entries) . PHP_EOL,
                'subnets' => $dhcpConfigModel->subnets . PHP_EOL,
                'groups' => $dhcpConfigModel->groups . PHP_EOL,
                'footer' => $dhcpConfigModel->footer . PHP_EOL
            ])->render();
        });
    }
}
