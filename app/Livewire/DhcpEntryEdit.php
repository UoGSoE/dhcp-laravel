<?php

namespace App\Livewire;

use App\Models\DhcpEntry;
use Illuminate\Support\Facades\Route;
use Livewire\Component;

class DhcpEntryEdit extends Component
{
    public ?DhcpEntry $dhcpEntry;

    public string $id;
    public ?string $hostname;
    public ?string $ipAddress;
    public ?string $owner;
    public ?string $addedBy;
    public ?bool $isSsd;
    public ?bool $isActive;

    public ?array $macAddresses;

    public bool $macAddressValidationPasses;

    public function mount()
    {
        $this->dhcpEntry = Route::current()->parameter('dhcpEntry');
        $this->id = $this->dhcpEntry->id;
        $this->hostname = $this->dhcpEntry->hostname;
        $this->ipAddress = $this->dhcpEntry->ip_address;
        $this->owner = $this->dhcpEntry->owner;
        $this->addedBy = $this->dhcpEntry->added_by;
        $this->isSsd = $this->dhcpEntry->is_ssd;
        $this->isActive = $this->dhcpEntry->is_active;
        $this->macAddresses = $this->dhcpEntry->macAddresses->toArray();
    }

    public function render()
    {
        return view('livewire.dhcp.dhcp-entry-edit', []);
    }
}
