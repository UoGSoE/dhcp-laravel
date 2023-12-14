<?php

namespace App\Livewire;

use App\Models\DhcpEntry;
use App\Services\InputValidationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class DhcpEntryTable extends Component
{
    use WithPagination;

    public string $search = '';
    public int $perPage = 2;
    public string $sortField = 'created_at';
    public bool $sortAsc = true;
    public string $activeFilter = "true";
    public ?bool $active;
    // protected $queryString = ['search', 'perPage', 'sortField', 'sortAsc', 'active',];

    public bool $selectPage = false;
    public array $selected = [];
    public bool $selectAll = false;

    public array $editedEntries = [];
    public array $editRowActive = [];
    public array $validationErrors = [];

    public bool $showAlertMessage = false;

    public function rules(): array
    {
        return [
            // 'editedEntries.*.hostname' => 'required|unique:dhcp_entries,hostname',
            // 'editedEntries.*.mac_address' => 'required|unique:dhcp_entries,mac_address|mac_address',
            // 'editedEntries.*.ip_address' => 'nullable|ip|unique:dhcp_entries,ip_address',
            // 'editedEntries.*.owner' => 'required',
            // 'editedEntries.*.is_ssd' => 'required|boolean',
            // 'editedEntries.*.is_active' => 'required|boolean',


            'hostname' => 'required|unique:dhcp_entries,hostname',
            'mac_address' => 'required|unique:dhcp_entries,mac_address|mac_address',
            'ip_address' => 'nullable|ip|unique:dhcp_entries,ip_address',
            'owner' => 'required',
            'is_ssd' => 'required|boolean',
            'is_active' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            // 'editedEntries.*.hostname.required' => 'Hostname is required',
            // 'editedEntries.*.hostname.unique' => 'Hostname is already in use',
            // 'editedEntries.*.mac_address.required' => 'MAC address is required',
            // 'editedEntries.*.mac_address.unique' => 'MAC address is already in use',
            // 'editedEntries.*.mac_address.mac_address' => 'MAC address is not valid',
            // 'editedEntries.*.ip_address.ip' => 'IP address is not valid',
            // 'editedEntries.*.ip_address.unique' => 'IP address is already in use',
            // 'editedEntries.*.owner.required' => 'Owner is required',
            // 'editedEntries.*.is_ssd.required' => 'Input is required',
            // 'editedEntries.*.is_ssd.boolean' => 'Input must be a boolean',
            // 'editedEntries.*.is_active.required' => 'Input is required',
            // 'editedEntries.*.is_active.boolean' => 'Input must be a boolean',

            'hostname.required' => 'Hostname is required',
            'hostname.unique' => 'Hostname is already in use',
            'mac_address.required' => 'MAC address is required',
            'mac_address.unique' => 'MAC address is already in use',
            'mac_address.mac_address' => 'MAC address is not valid',
            'ip_address.ip' => 'IP address is not valid',
            'ip_address.unique' => 'IP address is already in use',
            'owner.required' => 'Owner is required',
            'is_ssd.required' => 'Input is required',
            'is_ssd.boolean' => 'Input must be a boolean',
            'is_active.required' => 'Input is required',
            'is_active.boolean' => 'Input must be a boolean',
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

    public function updateActiveAttribute(): void
    {
        if ($this->activeFilter == "true") {
            $this->active = true;
        } elseif ($this->activeFilter == "false") {
            $this->active = false;
        } else {
            $this->active = null;
        }

    }

    public function updatedActiveFilter(): void
    {
        $this->updateActiveAttribute();
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
        $this->updateActiveAttribute();

        if ($this->selectAll) {
            $this->selected = $this->results->get()->pluck('id')->map(fn ($id) => (string) $id)->toArray();
        }

        return view('livewire.dhcp-entry.dhcp-entry-table', [
            'dhcpEntries' => $this->results->paginate($this->perPage),
        ]);

    }

    // Runs when all entries on current page are selected
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


    public function prepareEditDhcpRow(array $dhcpEntry)
    {
        $this->editedEntries[$dhcpEntry['id']] = [
            'id' => $dhcpEntry['id'],
            'hostname'  => $dhcpEntry['hostname'],
            'mac_address' => $dhcpEntry['mac_address'],
            'ip_address' => $dhcpEntry['ip_address'],
            'owner' => $dhcpEntry['owner'],
            'is_ssd' => $dhcpEntry['is_ssd'],
            'is_active' => $dhcpEntry['is_active'],
        ];

        $this->editRowActive[$dhcpEntry['id']] = true;
    }

    public function saveUpdatedRow(array $dhcpEntry)
    {
        $this->validationErrors = [];
        $this->showAlertMessage = false;

        $changedValues = array_diff_assoc($this->editedEntries[$dhcpEntry['id']], $dhcpEntry);

        // Row is unchanged, cancel edit
        if (empty($changedValues)) {
            $this->cancelEditField($dhcpEntry['id']);
            return;
        }

        $this->validationErrors = InputValidationService::validateInput(
            $changedValues,
            $this->rules(),
            $this->messages(),
            $this->validationErrors
        );

        if (!empty($this->validationErrors)) {
            session()->flash('error', 'DHCP entry update unsuccessful.');
            $this->showAlertMessage = true;
            return;
        }

        $dhcpEntryModel = DhcpEntry::find($dhcpEntry['id']);
        foreach($changedValues as $key => $value) {
            $dhcpEntryModel->$key = $value;
        }

        $dhcpEntryModel->save();
        session()->flash('success', 'DHCP entry updated successfully.');
        $this->showAlertMessage = true;

        $this->cancelEditField($dhcpEntry['id']);
    }

    public function cancelEditField(string $id): void
    {
        unset($this->editedEntries[$id]);
        unset($this->editRowActive[$id]);

        $this->validationErrors = [];
    }
}
