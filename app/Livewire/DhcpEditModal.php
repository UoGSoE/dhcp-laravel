<?php

namespace App\Livewire;

use App\Models\DhcpEntry;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use LivewireUI\Modal\ModalComponent;

class DhcpEditModal extends ModalComponent
{
    public array $selected;

    public function mount(array $selected)
    {
        $this->selected = DhcpEntry::with('notes')->find($selected)->all();
    }

    public function render()
    {
        return view('livewire.dhcp-edit-modal');
    }
}
