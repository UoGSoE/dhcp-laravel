<?php

namespace App\Models;

use Database\Factories\DhcpSectionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DhcpSection extends Model
{
    /** @use HasFactory<DhcpSectionFactory> */
    use HasFactory;

    protected $fillable = [
        'section',
        'body',
    ];
}
