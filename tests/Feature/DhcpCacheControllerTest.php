<?php

namespace Tests\Feature;

use App\Models\DhcpConfig;
use App\Models\DhcpEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class DhcpCacheControllerTest extends TestCase
{
    use RefreshDatabase;

    public ?DhcpConfig $dhcpConfig = null;
    public ?DhcpEntry $dhcpEntry1 = null;
    public ?DhcpEntry $dhcpEntry2 = null;
    public ?DhcpEntry $dhcpEntry3 = null;
    public ?DhcpEntry $dhcpEntry4 = null;

    public function setUp(): void
    {
        parent::setUp();

        $this->dhcpConfig = DhcpConfig::factory()->create([
            'header' => 'DHCP Header',
            'subnets' => 'DHCP Subnets',
            'groups' => 'DHCP Groups',
            'footer' => 'DHCP Footer #### End of DHCP'
        ]);

        $this->dhcpEntry1 = DhcpEntry::factory()->create([
            'hostname' => 'first-hostname',
            'mac_address' => '20:20:20:20:20:21',
            'ip_address' => null,
            'owner' => 'a@a.com',
            'added_by' => 'Test User',
            'is_ssd' => true,
            'is_active' => false
        ]);

        $this->dhcpEntry2 = DhcpEntry::factory()->create([
            'hostname' => 'second-hostname',
            'mac_address' => '20:20:20:20:20:22',
            'ip_address' => '192.168.0.1',
            'owner' => 'b@b.com',
            'added_by' => 'Test User',
            'is_ssd' => false,
            'is_active' => true
        ]);

        $this->dhcpEntry3 = DhcpEntry::factory()->create([
            'hostname' => 'third-hostname',
            'mac_address' => '20:20:20:20:20:23',
            'ip_address' => '192.168.0.1',
            'owner' => 'c@c.com',
            'added_by' => 'Test User',
            'is_ssd' => false,
            'is_active' => true
        ]);

        $this->dhcpEntry4 = DhcpEntry::factory()->create([
            'hostname' => 'fourth-hostname',
            'mac_address' => '20:20:20:20:20:24',
            'ip_address' => '192.168.0.1',
            'owner' => 'd@d.com',
            'added_by' => 'Test User',
            'is_ssd' => false,
            'is_active' => true
        ]);
    }

    public function test_dhcp_cache(): void
    {
        // Cache file does not yet exist
        $file = Cache::get('dhcpFile');
        $this->assertNull($file);

        $response = $this->get(Route('dhcp-cache'));
        $response->assertStatus(200);

        $file = Cache::get('dhcpFile');
        $this->assertNotNull($file);

        $this->assertStringContainsString('DHCP Header', $file);
        $this->assertStringContainsString('DHCP Subnets', $file);
        $this->assertStringContainsString('DHCP Groups', $file);
        $this->assertStringContainsString('DHCP Footer #### End of DHCP', $file);
        $this->assertStringContainsString('first-hostname', $file);
        $this->assertStringContainsString('second-hostname', $file);
        $this->assertStringContainsString('third-hostname', $file);
        $this->assertStringContainsString('fourth-hostname', $file);
    }

    public function test_updating_dhcp_entry_clears_cache(): void
    {
        Cache::put('dhcpFile', 'test123');
        $this->dhcpEntry1->update([
            'hostname' => 'updated-hostname',
        ]);

        // Cache file is removed
        $file = Cache::get('dhcpFile');
        $this->assertNull($file);
    }

    public function test_deleting_dhcp_entry_clears_cache(): void
    {
        Cache::put('dhcpFile', 'test123');
        $this->dhcpEntry1->delete();

        // Cache file is removed
        $file = Cache::get('dhcpFile');
        $this->assertNull($file);
    }
}
