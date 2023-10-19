<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MissingAttributeException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class DhcpEntry extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'id',
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

    public function save(array $options = [])
    {
        $this->checkIfValueAlreadyExists('hostname', 'This hostname is already in use.');
        $this->checkIfValueAlreadyExists('ip_address', 'This IP address is already in use.');

        return parent::save($options);
    }

    private function checkIfValueAlreadyExists(string $property, string $message): void
    {
        if (isset($this->attributes[$property]) && $this->attributes[$property] !== null) {
            $existing = self::where($property, $this->attributes[$property])->first();

            if ($existing && $existing->id !== $this->id) {
                throw ValidationException::withMessages([
                    $property => [$message]
                ]);
            }
        }
    }
}
