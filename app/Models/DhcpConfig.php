<?php

namespace App\Models;

use App\Events\DhcpChangedEvent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DhcpConfig extends Model
{
    use HasFactory;

    protected $table = 'dhcp_config';

    protected $fillable = [
        'header',
        'subnets',
        'groups',
        'footer'
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
}
