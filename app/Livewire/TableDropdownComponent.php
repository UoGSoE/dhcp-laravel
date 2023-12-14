<?php

namespace App\Livewire;

use Livewire\Component;

class TableDropdownComponent extends Component
{

    public string $id;
    public string $value;
    public bool $active = false;

    public function render()
    {
        return view('livewire.table-dropdown-component');
    }
}
