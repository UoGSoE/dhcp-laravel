<?php

namespace App\Livewire;

use App\Models\MacAddress;
use App\Services\InputValidationService;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class MacAddressComponent extends Component
{
    public ?string $action;
    public $macAddresses;
    public array $validationErrors = [];

    public function render()
    {
        return view('livewire.dhcp.mac-address-component', [
            'macAddresses' => $this->macAddresses
        ]);
    }

    public function addMacAddress(): void
    {
        $macAddressData = [
            'macAddress' => '',
        ];

        $this->macAddresses[] = $macAddressData;

        // Validate the newly added mac address
        // $this->updated('macAddresses.' . (count($this->macAddresses) - 1) . '.macAddress');
    }

    public function updated($field): void
    {
        $this->validationErrors = InputValidationService::validateInput(
            ['macAddresses' => $this->macAddresses],
            ['macAddresses.*.macAddress' => 'required|regex:/^([0-9A-Fa-f]{2}[:]){5}([0-9A-Fa-f]{2})$/'],
            [
                'required' => 'This field must not be empty',
                'regex' => 'This field must be a valid MAC address'
            ],
            $field,
            $this->validationErrors
        );

        // Update error bag
        $this->setErrorBag($this->validationErrors);

        if ($this->validationErrors) {
            $this->dispatch('updateValidationPassStatus', validationPassStatus: false);
            return;
        }

        // Only emits event to update macAddresses array if validation passes
        if ($this->macAddresses) {
            $this->dispatch('updateValidationPassStatus', validationPassStatus: true);
            $this->dispatch('macAddressesUpdated', macAddresses: $this->macAddresses);
        }
    }
}
