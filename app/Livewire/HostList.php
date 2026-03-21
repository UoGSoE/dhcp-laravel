<?php

namespace App\Livewire;

use App\Models\Host;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class HostList extends Component
{
    #[Url]
    public $search = '';

    #[On('host-saved')]
    public function refreshList(): void
    {
        // Livewire re-renders automatically
    }

    public function render()
    {
        $query = Host::query();

        if ($this->search) {
            $query->search($this->search)->orderBy('hostname');
        } else {
            $query->orderByDesc('last_updated')->orderBy('hostname')->limit(50);
        }

        $hosts = $query->get();

        return view('livewire.host-list', compact('hosts'));
    }
}
