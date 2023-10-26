<?php

namespace App\Livewire;

use App\Models\DhcpEntry;
use App\Services\InputValidationService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Ohffs\Ldap\LdapService;
use Ramsey\Uuid\Uuid;

class DhcpEntryCreate extends Component
{
    public string $id;
    public string $macAddress = '';
    public string $hostname = '';
    public ?string $ipAddress = null;
    public string $owner = '';
    public string $addedBy = '';
    public bool $isSsd = false;
    public bool $isActive = true;

    public array $validationErrors = [];

    public array $notes = [];

    public function mount()
    {
        $this->id = Uuid::uuid4()->toString();
    }

    public function render()
    {
        return view('livewire.dhcp.dhcp-entry-create');
    }

    public function updated($field): void
    {
        // If IP address updated after validation, remove error
        if ($field == 'ipAddress' && array_key_exists('ip_address', $this->validationErrors)) {
            unset($this->validationErrors['ip_address']);
        }

        $this->validationErrors = InputValidationService::validateInput(
            ['owner' => $this->owner, 'macAddress' => $this->macAddress],
            ['owner' => 'required', 'macAddress' => 'required|mac_address'],
            [
                'required' => 'This field must not be empty',
                'mac_address' => 'This field must be a valid MAC address'
            ],
            $field,
            $this->validationErrors,
        );

        $this->setErrorBag($this->validationErrors);
    }

    public function createDhcpEntry(LdapService $ldapService): void
    {
        $this->addedBy = Auth::user()->getFullNameAttribute();
        // $user = $ldapService->findUser(Auth::user()->guid);
        // $this->addedBy = $user->username . ' (' . $user->forenames . ')';

        if (!$this->hostname || $this->hostname === '') {
            $this->hostname = 'eng-pool-' . $this->id;
        }

        $this->validate([
            'macAddress' => 'required',
            'hostname' => 'required',
            'owner' => 'required',
            'addedBy' => 'required',
            'isSsd' => 'required',
            'isActive' => 'required',
        ]);

        $dhcpEntryData = [
            'id' => $this->id,
            'mac_address' => $this->macAddress,
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
}
