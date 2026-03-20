<?php

namespace App\Livewire;

use App\Enums\HostStatus;
use App\Models\Checkin;
use App\Models\Host;
use Livewire\Component;

class HostForm extends Component
{
    public ?Host $host = null;

    public string $mac = '';

    public string $owner = '';

    public string $ip = '';

    public string $hostname = '';

    public string $status = 'Enabled';

    public string $ssd = 'No';

    public string $notes = '';

    public function mount(?Host $host = null): void
    {
        if ($host?->exists) {
            $this->host = $host;
            $this->mac = $host->mac;
            $this->owner = $host->owner;
            $this->ip = $host->ip ?? '';
            $this->hostname = $host->hostname ?? '';
            $this->status = $host->status->uiEquivalent()->value;
            $this->ssd = $host->ssd;
            $this->notes = $host->notes ?? '';
        }
    }

    public function save(): mixed
    {
        $rules = [
            'mac' => ['required', 'regex:/^([0-9a-fA-F]{2}[:\-]?){5}[0-9a-fA-F]{2}$|^[0-9a-fA-F]{12}$/'],
            'owner' => ['required', 'email'],
            'ip' => ['nullable', 'regex:/^(130\.209|172\.20)\.\d{1,3}\.\d{1,3}$/'],
            'hostname' => ['nullable', 'regex:/^[a-zA-Z0-9\-\.]+$/'],
            'status' => ['required', 'in:Enabled,Disabled'],
            'ssd' => ['required', 'in:Yes,No'],
            'notes' => ['nullable'],
        ];

        if ($this->ip) {
            $uniqueRule = 'unique:hosts,ip';
            if ($this->host) {
                $uniqueRule .= ','.$this->host->id;
            }
            $rules['ip'][] = $uniqueRule;
        }

        $this->validate($rules);

        $data = [
            'mac' => $this->mac,
            'owner' => $this->owner,
            'ip' => $this->ip ?: null,
            'hostname' => $this->hostname ?: null,
            'status' => HostStatus::from($this->status),
            'ssd' => $this->ssd,
            'notes' => $this->notes ?: null,
        ];

        if ($this->host) {
            $this->host->update($data);
            $host = $this->host;
        } else {
            $data['added_by'] = auth()->user()->username;
            $host = Host::create($data);
        }

        $normalisedMac = $host->fresh()->mac;
        $duplicateCount = Host::where('mac', $normalisedMac)->where('id', '!=', $host->id)->count();

        if ($duplicateCount > 0) {
            session()->flash('warning', 'Warning : entry saved, but duplicate MAC address');

            return $this->redirect('/?search='.$normalisedMac);
        }

        session()->flash('success', 'Host saved.');

        return $this->redirect('/');
    }

    public function delete(): mixed
    {
        $this->host->delete();
        Checkin::truncate();

        session()->flash('success', 'Host deleted.');

        return $this->redirect('/');
    }

    public function render()
    {
        return view('livewire.host-form');
    }
}
