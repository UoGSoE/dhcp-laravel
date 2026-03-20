<?php

namespace App\Livewire;

use App\Models\Checkin;
use App\Models\DhcpSection;
use Livewire\Component;

class DhcpSectionEditor extends Component
{
    public DhcpSection $section;

    public string $body = '';

    public function mount(string $sectionName): void
    {
        $this->section = DhcpSection::where('section', $sectionName)->firstOrFail();
        $this->body = $this->section->body ?? '';
    }

    public function save(): void
    {
        $this->section->update(['body' => $this->body]);
        Checkin::truncate();
    }

    public function render()
    {
        return view('livewire.dhcp-section-editor');
    }
}
