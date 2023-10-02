<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DhcpEntryController extends Controller
{
    public function create(Request $request)
    {
        $validated = $request->validate([
            'hostname' => 'required',
            'ip_address' => 'required|ipv4',
            'owner' => 'required',
            'added_by' => 'required',
            'is_ssd' => 'required',
            'is_active' => 'required|boolean'
        ]);

        $dhcpEntry = \App\Models\DhcpEntry::create($validated);

        return redirect()->route('dhcp-entry.show', ['dhcpEntry' => $dhcpEntry->id]);
    }

    public function show(Request $request, \App\Models\DhcpEntry $dhcpEntry)
    {
        return view('dhcp-entry.show', [
            'dhcpEntry' => $dhcpEntry
        ]);
    }
}
