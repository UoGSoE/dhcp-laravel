<?php

namespace App\Livewire;

use App\Models\DhcpEntry;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use Livewire\WithPagination;

class DhcpEntryTable extends Component
{
    use WithPagination;

    public array $dhcpEntries;
    public string $search = '';
    public int $perPage = 10;

    public function __construct()
    {
        $this->dhcpEntries = DhcpEntry::with(['macAddresses', 'notes'])->paginate($this->perPage)->items();
    }

    public function render()
    {
        // dump($this->dhcpEntries);

        return view('livewire.dhcp.dhcp-entry-table', [
            'dhcpEntries' => $this->dhcpEntries
        ]);
    }
}
