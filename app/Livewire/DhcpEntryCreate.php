<?php

namespace App\Livewire;

use App\Models\DhcpEntry;
use App\Models\Note;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Ramsey\Uuid\Uuid;

class DhcpEntryCreate extends Component
{
    #[Rule([
        'macAddress' => 'required|unique:dhcp_entries,mac_address|mac_address',
        'hostname' => 'required_unless:ipAddress,null|unique:dhcp_entries,hostname',
        'ipAddress' => 'nullable|ip|unique:dhcp_entries,ip_address',
        'owner' => 'required',
        'addedBy' => 'required',
        'isSsd' => 'required|boolean',
        'isActive' => 'required|boolean',
        'isImported' => 'required|boolean',
        'note' => 'nullable|string'
    ])]

    public string $id;
    public string $macAddress = '';
    public string $hostname = '';
    public ?string $ipAddress = null;
    public string $owner = '';
    public string $addedBy = '';
    public bool $isSsd = false;
    public bool $isActive = true;
    public bool $isImported = false;

    public ?string $note = null;

    public function mount()
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

        if (!$this->hostname || $this->hostname === '') {
            $this->hostname = 'eng-pool-' . $this->id;
        }

        $this->validate();

        $dhcpEntryData = [
            'id' => $this->id,
            'mac_address' => $this->macAddress,
            'hostname' => $this->hostname,
            'ip_address' => $this->ipAddress,
            'owner' => $this->owner,
            'added_by' => $this->addedBy,
            'is_ssd' => $this->isSsd,
            'is_active' => $this->isActive,
            'is_imported' => $this->isImported,
        ];

        $dhcpEntry = DhcpEntry::create($dhcpEntryData);

        if (!$this->note || $this->note == "") {
            $this->redirect(route('dhcp-entries'));
            return;
        }

        $noteData = [
            'note' => strip_tags($this->note),
            'created_by' => $this->addedBy,
        ];

        $dhcpEntry->notes()->create($noteData);

        session()->flash('success', 'DHCP entry created successfully.');

        $this->redirect(route('dhcp-entries'));
    }
}
