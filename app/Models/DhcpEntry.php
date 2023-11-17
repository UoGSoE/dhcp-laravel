<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DhcpEntry extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'id',
        'hostname',
        'mac_address', // 'macAddress' in Livewire
        'ip_address', // 'ipAddress' in Livewire
        'owner',
        'added_by', // 'addedBy' in Livewire
        'is_ssd', // 'isSsd' in Livewire
        'is_active', // 'isActive' in Livewire
        'is_imported' // 'isImported' in Livewire
    ];

    public function notes()
    {
        return $this->hasMany(Note::class);
    }
}
