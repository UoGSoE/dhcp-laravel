<?php

namespace App\Livewire;

use App\Models\DhcpEntry;
use App\Models\MacAddress;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Ramsey\Uuid\Uuid;

class DhcpEntryCreate extends Component
{
    public string $dhcpEntryId = '';
    public string $hostname = '';
    public string $ipAddress = '';
    public string $owner = '';
    public string $addedBy = '';
    public bool $isSsd = false;
    public bool $isActive = true;

    public array $macAddresses = [];

    public array $notes =[];

    public function __construct()
    {
        $this->dhcpEntryId = Uuid::uuid4()->toString();
        $this->macAddresses[] = [
            'macAddress' => '',
        ];
        $this->addedBy = Auth::user()->guid;
    }

    public function render()
    {
        return view('livewire.dhcp.dhcp-entry-create');
    }

    public function createDhcpEntry()
    {
        $this->validate([
            'hostname' => 'required',
            'owner' => 'required',
            'addedBy' => 'required',
            'isSsd' => 'required',
            'isActive' => 'required'
        ]);

        //TODO add error handling when validation fails

        $dhcpEntryData = [
            'id' => $this->dhcpEntryId,
            'hostname' => $this->hostname,
            'ip_address' => $this->ipAddress,
            'owner' => $this->owner,
            'added_by' => $this->addedBy,
            'is_ssd' => $this->isSsd,
            'is_active' => $this->isActive
        ];

        $dhcpEntry = new DhcpEntry($dhcpEntryData);
        // $dhcpEntry->save();

        foreach ($this->macAddresses as $macAddress) {
            $macAddressData = [
                'mac_address' => $macAddress['macAddress'],
                'dhcp_entry_id' => $this->dhcpEntryId
            ];

            $macAddress = new MacAddress($macAddressData);
            // $macAddress->save();
        }
    }

    #[On('macAddressesUpdated')]
    public function updateMacAddresses($macAddresses)
    {
        $this->macAddresses = $macAddresses;
    }
}
