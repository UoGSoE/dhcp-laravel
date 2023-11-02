<?php

namespace Tests\Feature;

use App\Livewire\DhcpEntryCreate;
use App\Livewire\Homepage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DhcpEntryCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_dhcp_entry_can_be_created(): void
    {
        $user = User::factory()->make();

        $response = Livewire::actingAs($user)->test(DhcpEntryCreate::class)
            ->set('hostname', 'test-hostname')
            ->set('ip_address', '192.168.0.1')
            ->set('owner', 'test-owner')
            ->set('added_by', 'test-added-by')
            ->set('is_ssd', false)
            ->set('is_active', true)
            ->call('createDhcpEntry')
            ->assertHasNoErrors(
                'hostname',
                'ip_address',
                'owner',
                'added_by',
                'is_ssd',
                'is_active'
            )
            ->assertStatus(200);

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

    public function test_dhcp_entry_fails_when_data_is_valid(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = Livewire::test(DhcpEntryCreate::class)
            ->set('hostname', 'test-hostname-123')
            ->set('ip_address', '')
            ->set('owner', '')
            ->set('added_by', '')
            ->set('is_ssd', '')
            ->set('is_active', '')
            ->call('createDhcpEntry')
            ->assertHasErrors(
                'ip_address',
                'owner',
                'added_by',
                'is_ssd',
                'is_active'
            );

        $this->assertDatabaseMissing('dhcp_entries', [
            'hostname' => 'test-hostname-123',
            'ip_address' => '',
            'owner' => '',
            'added_by' => '',
            'is_ssd' => '',
            'is_active' => ''
        ]);
    }

    public function test_livewire_component_is_rendered(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dhcp-entry.create'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(DhcpEntryCreate::class);
    }

}
