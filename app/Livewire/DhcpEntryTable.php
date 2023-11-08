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

    public bool $selectPage = false;
    public array $selected = [];
    public bool $selectAll = false;

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

    public function updatedselectPage(bool $value)
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
}
