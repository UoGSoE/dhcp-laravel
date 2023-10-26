<?php

namespace App\Livewire;

use App\Models\DhcpEntry;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Ohffs\Ldap\LdapService;

class DhcpEntryTable extends Component
{
    use WithPagination;

    public string $search = '';
    public int $perPage = 10;
    public string $sortField = 'created_at';
    public bool $sortAsc = true;
    public string $activeFilter = "";
    public ?bool $active = null;
    protected $queryString = ['search', 'perPage', 'sortField', 'sortAsc', 'active',];

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

    public function render()
    {
        return view('livewire.dhcp.dhcp-entry-table', [
            'dhcpEntries' => DhcpEntry::with(['notes'])
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
                ->paginate($this->perPage)
        ]);
    }
}
