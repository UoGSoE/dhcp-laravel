<?php

namespace App\Livewire;

use App\Models\DhcpEntry;
use App\Models\Note;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule as ValidationRule;
use Livewire\Attributes\Rule;
use Livewire\Component;

class DhcpEntryEdit extends Component
{
    public ?DhcpEntry $dhcpEntry;

    public string $id;
    public string $macAddress = '';
    public string $hostname = '';
    public ?string $ipAddress = null;
    public string $owner = '';
    public string $addedBy = '';
    public string $note = '';
    public ?bool $isSsd;
    public ?bool $isActive;
    public ?bool $isImported;
    public ?Collection $notes = null;

    public function rules()
    {
        return [
            'macAddress' => [
                'required',
                'mac_address',
                ValidationRule::unique('dhcp_entries', 'mac_address')->ignore($this->id)
            ],
            'hostname' => [
                'required_unless:ipAddress,null',
                ValidationRule::unique('dhcp_entries', 'hostname')->ignore($this->id)
            ],
            'ipAddress' => [
                'nullable',
                'ip',
                ValidationRule::unique('dhcp_entries', 'ip_address')->ignore($this->id)
            ],
            'owner' => 'required',
            'addedBy' => 'required',
            'isSsd' => 'required|boolean',
            'isActive' => 'required|boolean',
            'isImported' => 'required|boolean',
            'note' => 'nullable|string'
        ];
    }

    public function mount(DhcpEntry $dhcpEntry): void
    {
        $this->dhcpEntry = $dhcpEntry;
        $this->id = $dhcpEntry->id;
        $this->macAddress = $dhcpEntry->mac_address;
        $this->hostname = $dhcpEntry->hostname;
        $this->ipAddress = $dhcpEntry->ip_address;
        $this->owner = $dhcpEntry->owner;
        $this->addedBy = $dhcpEntry->added_by;
        $this->isSsd = $dhcpEntry->is_ssd;
        $this->isActive = $dhcpEntry->is_active;
        $this->isImported = $dhcpEntry->is_imported;
        $this->notes = $dhcpEntry->notes;
    }

    public function render()
    {
        return view('livewire.dhcp-entry.dhcp-entry-edit');
    }

    public function updated($field): void
    {
        if ($field == 'ipAddress') {
            $this->resetValidation('hostname');
        }

        $this->validateOnly($field);
    }

    public function saveDhcpEntry(): void
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

        $this->dhcpEntry->update($dhcpEntryData);

        if ($this->note) {
            $this->dhcpEntry->notes()->create([
                'note' => strip_tags($this->note),
                'created_by' => auth()->user()->full_name,
            ]);
        }

        $this->redirect(route('dhcp-entries'));
    }
}
