<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MacAddress extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'mac_address',
        'dhcp_entry_id'
    ];

    public function dhcpEntry()
    {
        return $this->belongsTo(DhcpEntry::class);
    }
}
