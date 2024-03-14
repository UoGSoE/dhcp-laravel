<?php

namespace App\Models;

use App\Events\DhcpChangedEvent;
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
        'is_imported', // 'isImported' in Livewire,
        'created_at', // 'createdAt' in Livewire,
        'updated_at' // 'updatedAt' in Livewire
    ];

    protected static function booted(): void
    {
        static::saved(function () {
            DhcpChangedEvent::dispatch();
        });

        static::deleted(function () {
            DhcpChangedEvent::dispatch();
        });
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    public function latestNote()
    {
        return $this->hasOne(Note::class)->latestOfMany();
    }

    public function scopeIsActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getIsSsdAttribute($value): bool
    {
        return boolval($value);
    }

    public function getIsActiveAttribute($value): bool
    {
        return boolval($value);
    }

    public function getIsImportedAttribute($value): bool
    {
        return boolval($value);
    }

    public function getDhcpFileFormat(): string
    {
        $formattedText = '';

        if (!$this->is_active) {
            $formattedText .= '### DISABLED ';
        }

        $formattedText .= "host {$this->hostname} { hardware ethernet {$this->mac_address}; ";

        if ($this->ip_address) {
            $formattedText .= "fixed-address {$this->ip_address}; default-lease-time 86400; max-lease-time 86400;";
        }

        $formattedText .= ' }';

        return $formattedText;
    }
}
