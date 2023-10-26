<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Ohffs\Ldap\LdapService;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    // use LogsActivity; // todo add activity log
    use HasUuids;

    // protected static $logFillable = true;
    // protected static $logOnlyDirty = true;
    protected $fillable = [
        'forenames', 'surname', 'email', 'guid',
    ];
    protected $hidden = [
        'password', 'remember_token'
    ];

    public function getFullNameAttribute()
    {
        return $this->forenames . ' ' . $this->surname;
    }
}
