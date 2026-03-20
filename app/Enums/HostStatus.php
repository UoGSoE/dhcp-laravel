<?php

namespace App\Enums;

enum HostStatus: string
{
    case Enabled = 'Enabled';
    case Disabled = 'Disabled';
    case Up = 'Up';
    case Down = 'Down';

    public function label(): string
    {
        return $this->value;
    }

    public function colour(): string
    {
        return match ($this) {
            self::Enabled, self::Up => 'green',
            self::Disabled => 'red',
            self::Down => 'amber',
        };
    }

    /**
     * Whether this status causes the host to be commented out in dhcpd.conf.
     * ONLY Disabled causes commenting — Up and Down do NOT.
     */
    public function isDisabledInConfig(): bool
    {
        return $this === self::Disabled;
    }

    /**
     * Map to the nearest UI equivalent for radio button selection.
     */
    public function uiEquivalent(): self
    {
        return match ($this) {
            self::Enabled, self::Up => self::Enabled,
            self::Disabled, self::Down => self::Disabled,
        };
    }
}
