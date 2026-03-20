<?php

namespace App\Models;

use App\Enums\HostStatus;
use Database\Factories\HostFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Host extends Model
{
    /** @use HasFactory<HostFactory> */
    use HasFactory;

    protected $fillable = [
        'hostname',
        'mac',
        'ip',
        'added_by',
        'owner',
        'added_date',
        'wireless',
        'status',
        'notes',
        'ssd',
        'last_updated',
    ];

    protected function casts(): array
    {
        return [
            'added_date' => 'date',
            'last_updated' => 'datetime',
            'status' => HostStatus::class,
        ];
    }

    // Lifecycle

    protected static function booted(): void
    {
        static::creating(function (Host $host) {
            $host->added_date ??= now()->toDateString();
            $host->wireless ??= 'Yes';
            $host->mac = static::normaliseMac($host->mac);
        });

        static::updating(function (Host $host) {
            $host->mac = static::normaliseMac($host->mac);
        });

        static::saving(function (Host $host) {
            $host->last_updated = now();
        });

        static::created(function (Host $host) {
            if (empty($host->hostname)) {
                $host->updateQuietly(['hostname' => "eng-pool-{$host->id}"]);
            }
        });
    }

    // Scopes

    public function scopeSearch($query, string $term): void
    {
        if (strtolower($term) === 'ssd') {
            $query->where('ssd', 'Yes');

            return;
        }

        if (strtolower($term) === 'disabled') {
            $query->where('status', HostStatus::Disabled);

            return;
        }

        $query->where(function ($q) use ($term) {
            $q->where('id', $term)
                ->orWhere('hostname', 'like', "%{$term}%")
                ->orWhere('mac', 'like', "%{$term}%")
                ->orWhere('owner', 'like', "%{$term}%")
                ->orWhere('notes', 'like', "%{$term}%")
                ->orWhere('ip', 'like', "%{$term}%")
                ->orWhere('added_by', 'like', "%{$term}%");
        });
    }

    // Accessors

    public function ownerName(): string
    {
        return str($this->owner)->before('@')->value();
    }

    public function addedByName(): string
    {
        return config('dhcp.guid_names')[$this->added_by] ?? $this->added_by;
    }

    // Custom methods

    /**
     * Normalise a MAC address to lowercase colon-separated format.
     */
    public static function normaliseMac(?string $mac): ?string
    {
        if ($mac === null) {
            return null;
        }

        $cleaned = strtolower(preg_replace('/[^a-fA-F0-9]/', '', $mac));

        if (strlen($cleaned) !== 12) {
            return $mac;
        }

        return implode(':', str_split($cleaned, 2));
    }

    /**
     * Generate a dhcpd.conf line for this host.
     */
    public function toDhcpConfigLine(): string
    {
        $parts = "host {$this->hostname} { hardware ethernet {$this->mac};";

        if ($this->ip) {
            $parts .= " fixed-address {$this->ip}; default-lease-time 86400; max-lease-time 86400;";
        }

        if ($this->ssd === 'Yes') {
            $parts .= ' option domain-name-servers '.config('dhcp.ssd_dns_servers').';';
        }

        $parts .= ' }';

        if ($this->status->isDisabledInConfig()) {
            return "### DISABLED \t{$parts}";
        }

        return "\t{$parts}";
    }
}
