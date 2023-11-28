<?php

namespace App\Livewire;

use App\Models\DhcpEntry;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class DhcpEntryTable extends Component
{
    use WithPagination;

    public string $search = '';
    public int $perPage = 5;
    public string $sortField = 'created_at';
    public bool $sortAsc = true;
    public string $activeFilter = "";
    public ?bool $active = null;
    // protected $queryString = ['search', 'perPage', 'sortField', 'sortAsc', 'active',];

    public bool $selectPage = false;
    public array $selected = [];
    public bool $selectAll = false;

    public array $editedEntries = [];
    public array $currentEditedEntry = [
        'id' => '',
        'field' => '',
        'originalValue' => '',
    ];

    public function rules(string $field = ''): array
    {
        if ($field == 'hostname') {
            return [
                'editedEntries.*.hostname' => 'required|unique:dhcp_entries,hostname',
            ];
        } elseif ($field == 'mac_address') {
            return [
                'editedEntries.*.mac_address' => 'required|unique:dhcp_entries,mac_address|mac_address',
            ];
        } elseif ($field == 'ip_address') {
            return [
                'editedEntries.*.ip_address' => 'nullable|ip|unique:dhcp_entries,ip_address',
            ];
        } elseif ($field == 'owner') {
            return [
                'editedEntries.*.owner' => 'required',
            ];
        } elseif ($field == 'is_ssd') {
            return [
                'editedEntries.*.is_ssd' => 'required',
            ];
        } elseif ($field == 'is_active') {
            return [
                'editedEntries.*.is_active' => 'required',
            ];
        } else {
            return [
                'editedEntries.*.hostname' => 'required_unless:editedEntries.*.ip_address,null|unique:dhcp_entries,hostname',
                'editedEntries.*.mac_address' => 'required|unique:dhcp_entries,mac_address|mac_address',
                'editedEntries.*.ip_address' => 'nullable|ip|unique:dhcp_entries,ip_address',
                'editedEntries.*.owner' => 'required',
            ];
        }
    }

    public function messages(): array
    {
        return [
            'editedEntries.*.hostname.required' => 'Hostname is required',
            'editedEntries.*.hostname.unique' => 'Hostname is already in use',
            'editedEntries.*.mac_address.required' => 'MAC address is required',
            'editedEntries.*.mac_address.unique' => 'MAC address is already in use',
            'editedEntries.*.mac_address.mac_address' => 'MAC address is not valid',
            'editedEntries.*.ip_address.ip' => 'IP address is not valid',
            'editedEntries.*.ip_address.unique' => 'IP address is already in use',
            'editedEntries.*.owner.required' => 'Owner is required',
            'editedEntries.*.is_ssd.required' => 'Input is required',
            'editedEntries.*.is_active.required' => 'Input is required',
        ];
    }

    public function sortBy($field): void
    {
        // If active field, change sort direction
        if ($field === $this->sortField) {
            $this->sortAsc = !$this->sortAsc;
        } else {
            $this->sortAsc = true;
        }

        $this->sortField = $field;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedActiveFilter(): void
    {
        if ($this->activeFilter == "true") {
            $this->active = true;
        } elseif ($this->activeFilter == "false") {
            $this->active = false;
        } else {
            $this->active = null;
        }
    }

    public function getResultsProperty()
    {
        return DhcpEntry::leftJoin('notes', function ($join) {
            $join->on('dhcp_entries.id', '=', 'notes.dhcp_entry_id');
            $join->whereRaw('notes.updated_at = (select max(`updated_at`) from notes where notes.dhcp_entry_id = dhcp_entries.id)');
        })->select('dhcp_entries.*', 'notes.note')
                ->where(function ($query) {
                    $query->where('mac_address', 'like', '%' . $this->search . '%')
                    ->orWhere('hostname', 'like', '%' . $this->search . '%')
                    ->orWhere('ip_address', 'like', '%' . $this->search . '%')
                    ->orWhere('added_by', 'like', '%' . $this->search . '%')
                    ->orWhere('owner', 'like', '%' . $this->search . '%')
                    ->orWhereHas('notes', function ($query) {
                        $query->where('note', 'like', '%' . $this->search . '%');
                    });
                })->when($this->active !== null, function ($query) {
                    $query->where('is_active', $this->active);
                })
                ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc');
    }

    public function render()
    {
        if ($this->selectAll) {
            $this->selected = $this->results->get()->pluck('id')->map(fn ($id) => (string) $id)->toArray();
        }

        return view('livewire.dhcp-entry.dhcp-entry-table', [
            'dhcpEntries' => $this->results->paginate($this->perPage),
        ]);

    }

    public function updatedSelectPage(bool $value)
    {
        $this->selected = $value
            ? $this->results->paginate($this->perPage)->pluck('id')->map(fn ($id) => (string) $id)->toArray()
            : [];
    }

    public function selectAllEntries()
    {
        $this->selectAll = true;
    }

    public function updatedSelected($value)
    {
        if (in_array($value, $this->selected)) {
            $this->selectAll = false;
            $this->selectPage = false;
        }
    }

    public function deleteDhcpEntry(DhcpEntry $dhcpEntry)
    {
        $dhcpEntry->delete();
    }

    public function deleteSelected(array $selected)
    {
        if (empty($selected)) {
            return;
        }

        DhcpEntry::destroy($selected);
    }

    // public function editDhcpEntry(array $dhcpEntry)
    // {
    //     $this->currentEditedEntry = [
    //         'id' => $dhcpEntry['id'],
    //         'hostname'  => $dhcpEntry['hostname'],
    //         'mac_address' => $dhcpEntry['mac_address'],
    //         'ip_address' => $dhcpEntry['ip_address'],
    //         'owner' => $dhcpEntry['owner'],
    //         'is_ssd' => $dhcpEntry['is_ssd'],
    //         'is_active' => $dhcpEntry['is_active'],
    //     ];

    //     $this->editedEntries[] = $dhcpEntry['id'];
    // }

    // Prepare to edit field
    public function editField(array $dhcpEntry, string $field): void
    {
        $this->currentEditedEntry = [
            'id' => $dhcpEntry['id'],
            'field' => $field,
            'originalValue' => $dhcpEntry[$field],
        ];

        $this->editedEntries[$dhcpEntry['id']][$field] = $dhcpEntry[$field];

        // If the field being edited is hostname, add ip address value for validation
        if ($field == 'hostname') {
            $this->editedEntries[$dhcpEntry['id']]['ip_address'] = $dhcpEntry['ip_address'];
        }
    }

    // Save new value for field
    public function updateField(array $dhcpEntry, string $field)
    {
        // Value is unchanged, cancel edit
        if ($this->editedEntries[$dhcpEntry['id']][$field] == $this->currentEditedEntry['originalValue']) {
            $this->cancelEditField($dhcpEntry['id']);
            return;
        }

        $this->validate($this->rules($field), $this->messages());
        if ($this->getErrorBag()->has($field)) {
            session()->flash('error', 'DHCP entry update  unsuccessful.');
            return;
        }

        $dhcpEntryModel = DhcpEntry::find($dhcpEntry['id']);
        $dhcpEntryModel->$field = $this->editedEntries[$dhcpEntry['id']][$field];
        $dhcpEntryModel->save();

        session()->flash('success', 'DHCP entry updated successfully.');

        $this->cancelEditField($dhcpEntry['id']);
    }

    public function cancelEditField(string $id): void
    {
        $this->currentEditedEntry = [
            'id' => '',
            'field' => '',
            'originalValue' => '',
        ];

        unset($this->editedEntries[$id]);

        $this->resetValidation();
    }
}
