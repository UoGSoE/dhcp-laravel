<?php

namespace App\Livewire;

use App\Models\MacAddress;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class MacAddressComponent extends Component
{
    public array $macAddresses;
    public array $validatedMacAddresses;
    public array $validationErrors = [];

    public function render()
    {
        return view('livewire.mac-address-component', [
            'macAddresses' => $this->macAddresses
        ]);
    }

    public function addMacAddress()
    {
        $macAddressData = [
            'macAddress' => '',
        ];

        $this->macAddresses[] = $macAddressData;

        // Validate the newly added mac address
        $this->updated('macAddresses.' . (count($this->macAddresses) - 1) . '.macAddress');
    }

    public function updated($field)
    {
        $validation = Validator::make(
            ['macAddresses' => $this->macAddresses],
            ['macAddresses.*.macAddress' => 'required|regex:/^([0-9A-Fa-f]{2}[:]){5}([0-9A-Fa-f]{2})$/'],
            [
                'required' => 'The field must not be empty',
                'regex' => 'The field must be a valid MAC address'
            ]
        );

        // Validation error for this field
        $error = $validation->errors()->get($field);

        // Save validation errors for this field
        if ($error) {
            $this->validationErrors[$field] = $error;
        } else {
            unset($this->validationErrors[$field]);
        }

        // Update error bag
        $this->setErrorBag($this->validationErrors);

        // Update validatedMacAddresses array
        foreach($this->macAddresses as $index => $macAddress) {
            if (!in_array("macAddresses.{$index}.macAddress", array_keys($this->validationErrors))) {
                $this->validatedMacAddresses[$index] = $macAddress['macAddress'];
            }
        }

        // Only emits event to update macAddresses array if validation passes
        $this->dispatch('macAddressesUpdated', macAddresses: $this->validatedMacAddresses);
    }
}
