<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DhcpEntryTest extends TestCase
{
    use RefreshDatabase;

    public function test_dhcp_entry_can_be_created()
    {
        $response = $this->post(route('dhcp-entry.create'), [
            'hostname' => 'test-hostname',
            'ip_address' => '192.168.0.1',
            'owner' => 'test-owner',
            'added_by' => 'test-added-by',
            'is_ssd' => false,
            'is_active' => true
        ]);

        $response->assertRedirect();

        tap(\App\Models\DhcpEntry::first(), function ($dhcpEntry) {
            $this->assertEquals('test-hostname', $dhcpEntry->hostname);
        });

        $this->assertDatabaseHas('dhcp_entries', [
            'hostname' => 'test-hostname',
            'ip_address' => '192.168.0.1',
            'owner' => 'test-owner',
            'added_by' => 'test-added-by',
            'is_ssd' => false,
            'is_active' => true
        ]);
    }

    public function test_dhcp_entry_data_must_be_valid()
    {
        $response = $this->from(route('dhcp-entry.create'))->post(route('dhcp-entry.create'), [
            'hostname' => 'test-hostname',
            'ip_address' => '',
            'owner' => '',
            'added_by' => '',
            'is_ssd' => 'abc',
            'is_active' => 'abc'
        ]);

        $response->assertSessionHasErrors([
            'ip_address',
            'owner',
            'added_by',
            'is_ssd',
            'is_active'
        ]);

        $response->assertRedirect(route('dhcp-entry.create'));

        $this->assertDatabaseMissing('dhcp_entries', [
            'hostname' => 'test-hostname',
            'ip_address' => '',
            'owner' => '',
            'added_by' => '',
            'is_ssd' => 'abc',
            'is_active' => 'abc'
        ]);
    }
}
