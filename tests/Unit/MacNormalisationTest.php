<?php

use App\Models\Host;

it('normalises colon-separated uppercase to lowercase', function () {
    expect(Host::normaliseMac('AA:BB:CC:DD:EE:FF'))->toBe('aa:bb:cc:dd:ee:ff');
});

it('normalises dash-separated to colon-separated', function () {
    expect(Host::normaliseMac('AA-BB-CC-DD-EE-FF'))->toBe('aa:bb:cc:dd:ee:ff');
});

it('normalises bare hex to colon-separated', function () {
    expect(Host::normaliseMac('AABBCCDDEEFF'))->toBe('aa:bb:cc:dd:ee:ff');
});

it('handles mixed case', function () {
    expect(Host::normaliseMac('aA:bB:cC:dD:eE:fF'))->toBe('aa:bb:cc:dd:ee:ff');
});

it('returns the original value for invalid MACs', function () {
    expect(Host::normaliseMac('not-a-mac'))->toBe('not-a-mac');
    expect(Host::normaliseMac('AA:BB:CC'))->toBe('AA:BB:CC');
    expect(Host::normaliseMac('AABB'))->toBe('AABB');
});

it('handles null', function () {
    expect(Host::normaliseMac(null))->toBeNull();
});
