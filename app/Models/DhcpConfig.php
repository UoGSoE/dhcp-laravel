<?php

namespace App\Models;

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
}
