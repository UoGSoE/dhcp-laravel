<?php

namespace Tests\Feature;

use App\Livewire\ExportComponent;
use App\Models\DhcpEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Tests\TestCase;

class ExportComponentTest extends TestCase
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
        $this->actingAs($this->user);

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

    public function test_component_renders(): void
    {
        $response = $this->get(route('export.index'));
        $response->assertStatus(200);
    }

    public function test_export_to_csv_successful(): void
    {
        $response = Livewire::test(ExportComponent::class)
            ->call('exportCsv');

        $this->assertArrayHasKey('download', $response->effects);
        $this->assertSame('text/csv', $response->effects['download']['contentType']);

        $response->assertFileDownloaded();
    }

    public function test_export_to_json_successful(): void
    {
        $response = Livewire::test(ExportComponent::class)
            ->call('exportJson');

        $this->assertArrayHasKey('download', $response->effects);
        $this->assertSame('application/json', $response->effects['download']['contentType']);

        $response->assertFileDownloaded();
    }
}
