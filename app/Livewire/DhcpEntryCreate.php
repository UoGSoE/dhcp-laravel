<?php

namespace App\Livewire;

use App\Models\DhcpEntry;
use App\Models\MacAddress;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Component;
use Ramsey\Uuid\Uuid;
use Livewire\Attributes\Rule;
use App\Services\InputValidationService;

class DhcpEntryCreate extends Component
{
    public string $id = '';
    public string $hostname = '';
    public string $ipAddress = '';
    public string $owner = '';
    public string $addedBy = '';
    public bool $isSsd = false;
    public bool $isActive = true;

    public array $macAddresses = [];
    public bool $macAddressValidationPasses = false;

    public array $validationErrors = [];

    public array $notes =[];

    public function __construct()
    {
        $this->id = Uuid::uuid4()->toString();
        $this->macAddresses[] = [
            'macAddress' => '',
        ];
        $this->addedBy = Auth::user()->guid;
    }

    public function render()
    {
        return view('livewire.dhcp.dhcp-entry-create');
    }

    public function updated($field)
    {
        $this->validationErrors = InputValidationService::validateInput(
            ['hostname' => $this->hostname, 'owner' => $this->owner],
            ['hostname' => 'required', 'owner' => 'required'],
            ['required' => 'This field must not be empty'],
            $field,
            $this->validationErrors,
        );

        $this->setErrorBag($this->validationErrors);
    }

    public function createDhcpEntry()
    {
        $this->validate([
            'hostname' => 'required',
            'owner' => 'required',
            'isSsd' => 'required',
            'isActive' => 'required'
        ]);

        $dhcpEntryData = [
            'id' => $this->id,
            'hostname' => $this->hostname,
            'ip_address' => $this->ipAddress,
            'owner' => $this->owner,
            'added_by' => $this->addedBy,
            'is_ssd' => $this->isSsd,
            'is_active' => $this->isActive
        ];

        $dhcpEntry = new DhcpEntry($dhcpEntryData);

        try {
            $dhcpEntry->save();

            $macAddressData = [];
            foreach ($this->macAddresses as $key => $value) {
                $macAddressData[] = [
                    'id' => Uuid::uuid4()->toString(),
                    'mac_address' => $value['macAddress'],
                    'dhcp_entry_id' => $this->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                $macAddress = new MacAddress([
                    'mac_address' => $value['macAddress'],
                    'dhcp_entry_id' => $this->id
                ]);
            }

            MacAddress::insert($macAddressData);

            $this->redirect(route('dhcp-entries'));

        } catch (Exception $e) {
            if ($e instanceof ValidationException) {
                $errors = $e->validator->getMessageBag()->getMessages();
                foreach ($errors as $key => $error) {
                    $this->validationErrors[$key] = $error;
                }
                $this->setErrorBag($this->validationErrors);
            }
        }
    }

    #[On('macAddressesUpdated')]
    public function updateMacAddresses($macAddresses)
    {
        $this->macAddresses = $macAddresses;
    }

    #[On('updateValidationPassStatus')]
    public function updateMacValidationStatus($validationPassStatus)
    {
        $this->macAddressValidationPasses = $validationPassStatus;
    }
}
