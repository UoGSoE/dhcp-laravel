<?php

namespace Tests\Feature;

use App\Livewire\DhcpEntryCreate;
use App\Livewire\Homepage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class DhcpEntryCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_livewire_component_is_rendered(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dhcp-entry.create'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(DhcpEntryCreate::class);
    }

    public function test_dhcp_entry_and_note_can_be_created(): void
    {
        $user = User::factory()->make();
        $dhcpEntryId = '23a21296-cde5-4f41-9edf-e5cbbc71c43f';

        $response = Livewire::actingAs($user)->test(DhcpEntryCreate::class)
            ->set('id', $dhcpEntryId)
            ->set('macAddress', '20:20:20:20:20:20')
            ->set('hostname', 'test-hostname')
            ->set('ipAddress', '192.168.0.1')
            ->set('owner', 'test-owner')
            ->set('isSsd', true)
            ->set('isActive', false)
            ->set('note', 'This is a test note.')
            ->call('createDhcpEntry')
            ->assertHasNoErrors(
                'macAddress',
                'hostname',
                'ip_address',
                'owner',
                'added_by',
                'is_ssd',
                'is_active',
                'note'
            )
            ->assertStatus(200);

        $this->assertDatabaseHas('dhcp_entries', [
            'mac_address' => '20:20:20:20:20:20',
            'hostname' => 'test-hostname',
            'ip_address' => '192.168.0.1',
            'owner' => 'test-owner',
            'is_ssd' => 1,
            'is_active' => 0
        ]);

        $this->assertDatabaseHas('notes', [
            'note' => 'This is a test note.',
            'dhcp_entry_id' => $dhcpEntryId
        ]);
    }

    public function test_dhcp_entry_fails_when_data_is_invalid(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = Livewire::test(DhcpEntryCreate::class)
            ->set('hostname', 'test-hostname-123')
            ->set('macAddress', 123)
            ->set('ipAddress', '')
            ->set('owner', '')
            ->set('added_by', null)
            ->set('isSsd', '')
            ->set('isActive', '')
            ->call('createDhcpEntry')
            ->assertHasErrors(
                'macAddress',
                'ipAddress',
                'owner',
                'addedBy',
                'isSsd',
                'isActive'
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
}
