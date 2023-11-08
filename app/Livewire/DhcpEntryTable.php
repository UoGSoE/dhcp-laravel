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
    public int $perPage = 10;
    public string $sortField = 'created_at';
    public bool $sortAsc = true;
    public string $activeFilter = "";
    public ?bool $active = null;
    // protected $queryString = ['search', 'perPage', 'sortField', 'sortAsc', 'active',];

    public bool $selectAll = false;
    public array $selected = [];

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
                ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
                ->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.dhcp-entry.dhcp-entry-table', [
            'dhcpEntries' => $this->results,
        ]);

    }

    public function updatedSelectAll(bool $value)
    {
        $this->selected = $value
            ? $this->results->pluck('id')->map(fn ($id) => (string) $id)->toArray()
            : [];
    }

    public function selectRow(string $id): void
    {
        if (!in_array($id, $this->selected)) {
            $this->selected[] = $id;
            return;
        }

        $key = array_search($id, $this->selected);
        if (array_key_exists($key, $this->selected)) {
            $this->selected = array_values(array_diff($this->selected, [$id]));
        }
    }
}
