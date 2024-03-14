<?php

namespace App\Livewire;

use App\Models\DhcpConfig;
use Livewire\Component;

class DhcpConfigForm extends Component
{
    public ?DhcpConfig $dhcpConfig = null;
    public ?string $header = '';
    public ?string $subnets = '';
    public ?string $groups = '';
    public ?string $footer = '';

    public bool $showAlertMessage = false;

    public function mount(): void
    {
        $dhcpConfig = DhcpConfig::firstOrCreate();

        $this->dhcpConfig = $dhcpConfig;

        $this->header = $dhcpConfig->header;
        $this->subnets = $dhcpConfig->subnets;
        $this->groups = $dhcpConfig->groups;
        $this->footer = $dhcpConfig->footer;
    }

    public function render()
    {
        return view('livewire.dhcp-config.dhcp-config-form');
    }

    public function saveDhcpConfig(): void
    {
        $this->validate([
            'header' => 'nullable|string',
            'subnets' => 'nullable|string',
            'groups' => 'nullable|string',
            'footer' => 'nullable|string',
        ]);

        if (!$this->dhcpConfig) {
            $this->dhcpConfig = DhcpConfig::create();
        }

        $this->dhcpConfig->update([
            'header' => strip_tags($this->header),
            'subnets' => strip_tags($this->subnets),
            'groups' => strip_tags($this->groups),
            'footer' => strip_tags($this->footer),
        ]);

        $this->showAlertMessage = true;
        session()->flash('success', 'DHCP config saved successfully.');
        $this->js("window.scrollTo(0,0)");
    }
}
