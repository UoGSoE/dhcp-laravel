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

    public function setUp(): void
    {
        parent::setUp();

        DhcpEntry::factory()->create([
            'hostname' => 'test-hostname',
            'mac_address' => '20:20:20:20:20:20',
            'ip_address' => null,
            'owner' => 'a@a.com',
            'added_by' => 'Test User',
            'is_ssd' => true,
            'is_active' => false
        ]);

        DhcpEntry::factory()->create([
            'hostname' => 'hostname-number-two',
            'mac_address' => '20:20:20:20:20:21',
            'ip_address' => '192.168.0.1',
            'owner' => 'b@b.com',
            'added_by' => 'Test User',
            'is_ssd' => false,
            'is_active' => true
        ]);
    }

    public function test_livewire_component_is_rendered(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dhcp-entries'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(DhcpEntryTable::class);
    }


    public function test_table_is_searchable()
    {
        Livewire::test(DhcpEntryTable::class)
            ->assertSee('test-hostname')
            ->assertSee('hostname-number-two')
            ->set('search', 'test-hostname')
            ->assertSee('test-hostname')
            ->assertDontSee('hostname-number-two');
    }

    public function test_table_filters_active_and_inactive_entries()
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

    public function test_table_sorts_correctly()
    {
        Livewire::test(DhcpEntryTable::class)
            ->call('sortBy', 'hostname')
            ->set('sortAsc', false)
            ->assertSeeInOrder(['test-hostname', 'hostname-number-two'])
            ->set('sortAsc', true)
            ->call('sortBy', 'owner')
            ->assertSeeInOrder(['a@a.com', 'b@b.com']);
    }
}
