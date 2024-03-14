<?php

namespace App\Livewire;

use App\Models\DhcpEntry;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Ramsey\Uuid\Uuid;

class DhcpEntryCreate extends Component
{
    #[Validate([
        'hostname' => 'required_unless:ipAddress,null|unique:dhcp_entries,hostname',
        'macAddress' => 'required|mac_address|unique:dhcp_entries,mac_address',
        'ipAddress' => 'nullable|ip|unique:dhcp_entries,ip_address',
        'owner' => 'required',
        'addedBy' => 'required',
        'isSsd' => 'required|boolean',
        'isActive' => 'required|boolean',
        'isImported' => 'required|boolean',
        'note' => 'nullable|string'
    ])]

    public string $id;
    public string $hostname = '';
    public string $macAddress = '';
    public string $ipAddress = '';
    public string $owner = '';
    public string $addedBy = '';
    public string $note = '';
    public bool $isSsd = false;
    public bool $isActive = true;
    public bool $isImported = false;


    public function mount(): void
    {
        $this->id = Uuid::uuid4()->toString();
    }

    public function render()
    {
        return view('livewire.dhcp-entry.dhcp-entry-create');
    }

    public function updated($field): void
    {
        if ($field == 'ipAddress') {
            $this->resetValidation('hostname');
        }

        $this->validateOnly($field);
    }

    public function createDhcpEntry(): void
    {
        $this->addedBy = auth()->user()->full_name;

        if (!$this->hostname) {
            $this->hostname = 'eng-pool-' . $this->id;
        }

        $this->validate();

        $dhcpEntry = DhcpEntry::create([
            'id' => $this->id,
            'mac_address' => $this->macAddress,
            'hostname' => $this->hostname,
            'ip_address' => $this->ipAddress,
            'owner' => $this->owner,
            'added_by' => $this->addedBy,
            'is_ssd' => $this->isSsd,
            'is_active' => $this->isActive,
            'is_imported' => $this->isImported,
        ]);

        if ($this->note) {
            $dhcpEntry->notes()->create([
                'note' => strip_tags($this->note),
                'created_by' => $this->addedBy,
            ]);
        }

        session()->flash('success', 'DHCP entry created successfully.');

        $this->redirect(route('dhcp-entries'));
    }

    public function reformatMacAddress(): void
    {
        // Reformat mac address if entered without colons/hyphens and is correct length
        if (is_numeric($this->macAddress) && strlen($this->macAddress) == 12) {
            $pairs = str_split($this->macAddress, 2);
            $this->macAddress = implode(':', $pairs);
        }

        // Reformat mac address if entered with hyphens
        $this->macAddress = str_replace('-', ':', $this->macAddress);

        $this->validateOnly('macAddress');
    }
}
