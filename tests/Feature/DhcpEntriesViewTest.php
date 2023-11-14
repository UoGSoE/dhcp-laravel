<?php

namespace Tests\Feature;

use App\Livewire\DhcpEntryTable;
use App\Models\DhcpEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class DhcpEntriesViewTest extends TestCase
{
    use RefreshDatabase;

    public ?DhcpEntry $dhcpEntry1 = null;
    public ?DhcpEntry $dhcpEntry2 = null;
    public ?DhcpEntry $dhcpEntry3 = null;

    public function setUp(): void
    {
        parent::setUp();

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

    public function test_livewire_component_is_rendered(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dhcp-entries'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(DhcpEntryTable::class);
    }


    public function test_table_is_searchable(): void
    {
        Livewire::test(DhcpEntryTable::class)
            ->assertSee('test-hostname')
            ->assertSee('hostname-number-two')
            ->set('search', 'test-hostname')
            ->assertSee('test-hostname')
            ->assertDontSee('hostname-number-two');
    }

    public function test_table_filters_active_and_inactive_entries(): void
    {
        Livewire::test(DhcpEntryTable::class)
            // Active filter is set to "All" by default
            ->assertSee('test-hostname')
            ->assertSee('hostname-number-two')
            // Set active filter to "Active"
            ->set('activeFilter', 'true')
            ->assertDontSee('test-hostname')
            ->assertSee('hostname-number-two')
            // Set active filter to "Inactive"
            ->set('activeFilter', 'false')
            ->assertDontSee('hostname-number-two')
            ->assertSee('test-hostname');
    }

    public function test_table_sorts_correctly(): void
    {
        Livewire::test(DhcpEntryTable::class)
            ->call('sortBy', 'hostname')
            ->set('sortAsc', false)
            ->assertSeeInOrder(['test-hostname', 'hostname-number-two'])
            ->set('sortAsc', true)
            ->call('sortBy', 'owner')
            ->assertSeeInOrder(['a@a.com', 'b@b.com']);
    }

    public function test_entry_delete(): void
    {
        Livewire::test(DhcpEntryTable::class)
            ->call('deleteDhcpEntry', $this->dhcpEntry1->id)
            ->assertDontSee('test-hostname');

        $this->assertDatabaseMissing('dhcp_entries', [
            'id' => $this->dhcpEntry1->id,
            'hostname' => 'test-hostname'
        ]);
    }

    public function test_multiple_entry_delete(): void
    {
        Livewire::test(DhcpEntryTable::class)
            ->call('deleteSelected', [$this->dhcpEntry1->id, $this->dhcpEntry2->id])
            ->assertSee('hostname-number-three-test')
            ->assertDontSee('test-hostname')
            ->assertDontSee('hostname-number-two');

        $this->assertDatabaseCount('dhcp_entries', 1);
    }
}
