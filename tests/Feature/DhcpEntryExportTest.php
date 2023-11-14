<?php

namespace Tests\Feature;

use App\Models\DhcpEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DhcpEntryExportTest extends TestCase
{
    use RefreshDatabase;

    public ?User $user = null;
    public ?DhcpEntry $dhcpEntry1 = null;
    public ?DhcpEntry $dhcpEntry2 = null;
    public ?DhcpEntry $dhcpEntry3 = null;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->dhcpEntry1 = DhcpEntry::factory()->create([
            'hostname' => 'test-hostname',
            'mac_address' => '20:20:20:20:20:20',
            'ip_address' => null,
            'owner' => 'a@a.com',
            'added_by' => 'Test User',
            'is_ssd' => true,
            'is_active' => false
        ]);

        $this->dhcpEntry2 = DhcpEntry::factory()->create([
            'hostname' => 'hostname-number-two',
            'mac_address' => '20:20:20:20:20:21',
            'ip_address' => '192.168.0.1',
            'owner' => 'b@b.com',
            'added_by' => 'Test User',
            'is_ssd' => false,
            'is_active' => true
        ]);

        $this->dhcpEntry3 = DhcpEntry::factory()->create([
            'hostname' => 'hostname-number-three-test',
            'mac_address' => '20:20:20:20:20:22',
            'owner' => 'c@c.com',
            'added_by' => 'Test User',
            'is_ssd' => false,
            'is_active' => false
        ]);
    }

    public function test_export_to_csv(): void
    {
        $this->actingAs($this->user);
        $response = $this->get(route('export-csv'));

        $response->assertStatus(200);
    }

    public function test_export_to_json(): void
    {
        $this->actingAs($this->user);
        $response = $this->get(route('export-json'));

        $response->assertStatus(200);
    }
}
