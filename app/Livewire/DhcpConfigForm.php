<?php

namespace App\Livewire;

use App\Models\DhcpConfig;
use Livewire\Component;

class DhcpConfigForm extends Component
{
    public ?DhcpConfig $dhcpConfig = null;

    public ?string $header = null;
    public ?string $subnets = null;
    public ?string $groups = null;
    public ?string $footer = null;

    public bool $showSuccessMessage = false;

    public function mount()
    {
        $dhcpConfig = DhcpConfig::find(1);

        if (!$dhcpConfig) {
            return;
        }

        $this->dhcpConfig = $dhcpConfig;

        $this->header = $dhcpConfig->header;
        $this->subnets = $dhcpConfig->subnets;
        $this->groups = $dhcpConfig->groups;
        $this->footer = $dhcpConfig->footer;

        session()->flash('success', 'DHCP config saved successfully.');

    }

    public function render()
    {
        return view('livewire.dhcp-config.dhcp-config-form');
    }

    public function saveDhcpConfig()
    {
        $dhcpConfigData = [
            'header' => $this->header ? strip_tags($this->header) : $this->header,
            'subnets' => $this->subnets ? strip_tags($this->subnets) : $this->subnets,
            'groups' => $this->groups ? strip_tags($this->groups) : $this->groups,
            'footer' => $this->footer ? strip_tags($this->footer) : $this->footer,
        ];

        if (!$this->dhcpConfig) {
            $this->dhcpConfig = DhcpConfig::create();
        }

        // $dhcpConfig = DhcpConfig::findOr(1, function () {
        //     return DhcpConfig::create();
        // });

        $this->dhcpConfig->update($dhcpConfigData);

        $this->showSuccessMessage = true;
        session()->flash('success', 'DHCP config saved successfully.');
        $this->js("window.scrollTo(0,0)");
    }
}
