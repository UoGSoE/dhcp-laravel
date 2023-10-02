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
        'hostname',
        'ip_address',
        'owner',
        'added_by',
        'is_ssd',
        'is_active'
    ];

    public function macAddresses()
    {
        return $this->hasMany(MacAddress::class);
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }
}
