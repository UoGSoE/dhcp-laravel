<?php

use App\Enums\HostStatus;

it('only marks Disabled as disabled in config', function () {
    expect(HostStatus::Disabled->isDisabledInConfig())->toBeTrue();
    expect(HostStatus::Enabled->isDisabledInConfig())->toBeFalse();
    expect(HostStatus::Up->isDisabledInConfig())->toBeFalse();
    expect(HostStatus::Down->isDisabledInConfig())->toBeFalse();
});

it('maps UI equivalents correctly', function () {
    expect(HostStatus::Enabled->uiEquivalent())->toBe(HostStatus::Enabled);
    expect(HostStatus::Up->uiEquivalent())->toBe(HostStatus::Enabled);
    expect(HostStatus::Disabled->uiEquivalent())->toBe(HostStatus::Disabled);
    expect(HostStatus::Down->uiEquivalent())->toBe(HostStatus::Disabled);
});
