<?php

namespace Tests\Feature;

use App\Livewire\DhcpEntryEdit;
use App\Models\DhcpEntry;
use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DhcpEntryEditTest extends TestCase
{
    use RefreshDatabase;

    public function test_livewire_component_is_rendered(): void
    {
        $user = User::factory()->create();
        $dhcpEntry = DhcpEntry::factory()->hasNotes(2)->create();

        $response = $this->actingAs($user)->get(route('dhcp-entry.edit', ['dhcpEntry' => $dhcpEntry->id]));

        $response->assertStatus(200);
        $response->assertSeeLivewire(DhcpEntryEdit::class);
    }
}
