<?php

namespace App\Models;

use Database\Factories\CheckinFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checkin extends Model
{
    /** @use HasFactory<CheckinFactory> */
    use HasFactory;

    protected $fillable = [
        'hostname',
        'checked_in_at',
    ];

    protected function casts(): array
    {
        return [
            'checked_in_at' => 'datetime',
        ];
    }
}
