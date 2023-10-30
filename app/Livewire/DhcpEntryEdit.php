<?php

namespace App\Livewire;

use App\Models\DhcpEntry;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule as ValidationRule;
use Livewire\Attributes\Rule;
use Livewire\Component;

class DhcpEntryEdit extends Component
{
    // #[Rule([
    //     'macAddress' => [
    //         'required',
    //         'mac_address',
    //         ValidationRule::unique('dhcp_entries', 'mac_address')->ignore($this->id)
    //     ],
    //     'hostname' => 'required_unless:ipAddress,null|unique:dhcp_entries,hostname',
    //     'ipAddress' => 'nullable|ip|unique:dhcp_entries,ip_address',
    //     'owner' => 'required',
    //     'addedBy' => 'required',
    //     'isSsd' => 'required|boolean',
    //     'isActive' => 'required|boolean',
    //     'note' => 'nullable|string'
    // ])]

    public ?DhcpEntry $dhcpEntry;

    public string $id;
    public ?string $macAddress;
    public ?string $hostname;
    public ?string $ipAddress;
    public ?string $owner;
    public ?string $addedBy;
    public ?bool $isSsd;
    public ?bool $isActive;
    public ?string $note;

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
            'note' => 'nullable|string'
        ];
    }

    public function mount(DhcpEntry $dhcpEntry)
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
        $this->note = $dhcpEntry->notes->sortByDesc('updated_at')->first() ? $dhcpEntry->notes->sortByDesc('updated_at')->first()->note : null;
    }

    public function render()
    {
        return view('livewire.dhcp.dhcp-entry-edit');
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
            'is_active' => $this->isActive
        ];

        $noteData = [
            'note' => strip_tags($this->note),
            'created_by' => $this->addedBy,
        ];

        $dhcpEntry = DhcpEntry::findOrFail($this->id);
        $dhcpEntry->update($dhcpEntryData);

        if ($this->note !== $this->dhcpEntry->notes->sortByDesc('updated_at')->first()?->note) {
            $dhcpEntry->notes()->create($noteData);
        }

        $this->redirect(route('dhcp-entries'));
    }
}
