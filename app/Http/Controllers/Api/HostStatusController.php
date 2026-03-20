<?php

namespace App\Http\Controllers\Api;

use App\Enums\HostStatus;
use App\Http\Controllers\Controller;
use App\Models\Host;
use Illuminate\Http\JsonResponse;

class HostStatusController extends Controller
{
    public function online(Host $host): JsonResponse
    {
        $host->update(['status' => HostStatus::Up]);

        return response()->json(['Message' => 'OK', 'Status' => 1]);
    }

    public function offline(Host $host): JsonResponse
    {
        $host->update(['status' => HostStatus::Down]);

        return response()->json(['Message' => 'OK', 'Status' => 1]);
    }
}
