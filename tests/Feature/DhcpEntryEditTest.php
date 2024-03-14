<?php

namespace Tests\Feature;

use App\Livewire\DhcpEntryEdit;
use App\Models\DhcpEntry;
use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class DhcpEntryEditTest extends TestCase
{
    use RefreshDatabase;

    public ?User $user = null;
    public ?DhcpEntry $dhcpEntry = null;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->dhcpEntry = DhcpEntry::factory()->create([
            'hostname' => 'test-hostname',
            'mac_address' => '20:20:20:20:20:20',
            'ip_address' => null,
            'owner' => 'a@a.com',
            'added_by' => 'Test User',
            'is_ssd' => true,
            'is_active' => false
        ]);

        $note1 = Note::factory()
            ->for($this->dhcpEntry)
            ->create([
                'note' => 'Lorem ipsum dolor sit test.'
            ]);

        $note2 = Note::factory()
            ->for($this->dhcpEntry)
            ->create([
                'note' => 'Sit amet consectetur adipisicing elit.'
            ]);
    }

    public function test_livewire_component_is_rendered(): void
    {
        $response = $this->actingAs($this->user)->get(route('dhcp-entry.edit', ['dhcpEntry' => $this->dhcpEntry->id]));

        $response->assertStatus(200);
        $response->assertSeeLivewire(DhcpEntryEdit::class);
    }

    public function test_returns_404_if_dhcp_entry_does_not_exist(): void
    {
        $response = $this->actingAs($this->user)->get(route('dhcp-entry.edit', ['dhcpEntry' => '123']));
        $response->assertStatus(404);
    }

    public function test_livewire_component_loads_correct_dhcp_entry_data(): void
    {
        $this->actingAs($this->user)->get(route('dhcp-entry.edit', ['dhcpEntry' => $this->dhcpEntry->id]));

        Livewire::actingAs($this->user)
            ->test(DhcpEntryEdit::class, ['dhcpEntry' => $this->dhcpEntry])
            ->assertSet('hostname', 'test-hostname')
            ->assertSee('Lorem ipsum dolor sit test');
    }

    public function test_users_can_create_a_new_dhcp_entry()
    {
        Livewire::actingAs($this->user)
            ->test(DhcpEntryEdit::class, ['dhcpEntry' => $this->dhcpEntry])
            ->set('macAddress', '20:20:20:20:19:19')
            ->set('note', 'My new note.')
            ->call('saveDhcpEntry');

        $this->assertDatabaseHas('dhcp_entries', [
            'id' => $this->dhcpEntry->id,
            'mac_address' => '20:20:20:20:19:19',
            'hostname' => 'test-hostname',
        ]);

        $this->assertDatabaseHas('notes', [
            'note' => 'My new note.',
            'dhcp_entry_id' => $this->dhcpEntry->id,
        ]);
    }

    public function test_saving_a_dhcp_entry_fails_when_data_is_invalid()
    {
        DhcpEntry::factory()->create([
            'hostname' => 'second-hostname',
            'mac_address' => '20:20:20:20:19:19',
            'ip_address' => '192.168.0.1',
            'owner' => 'b@b.com',
            'added_by' => 'Test User',
            'is_ssd' => false,
            'is_active' => true
        ]);

        Livewire::actingAs($this->user)
            ->test(DhcpEntryEdit::class, ['dhcpEntry' => $this->dhcpEntry])
            // Set macAddress to already existing mac address
            ->set('macAddress', '20:20:20:20:19:19')
            ->call('saveDhcpEntry')
            ->assertHasErrors(
                'macAddress'
            )
            ->assertSee('The mac address has already been taken.');

        $this->assertDatabaseMissing('dhcp_entries', [
            'id' => $this->dhcpEntry->id,
            'mac_address' => '20:20:20:20:19:19',
            'hostname' => 'test-hostname',
        ]);
    }
}
