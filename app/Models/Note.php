<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'id',
        'note',
        'created_by',
        'dhcp_entry_id',
        'created_at',
        'updated_at',
    ];

    public function dhcpEntry()
    {
        return $this->belongsTo(DhcpEntry::class);
    }
}
