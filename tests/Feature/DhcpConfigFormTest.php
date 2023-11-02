<?php

namespace Tests\Feature;

use App\Livewire\DhcpConfigForm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DhcpConfigFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_livewire_component_is_rendered(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dhcp-config'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(DhcpConfigForm::class);
    }

    public function test_save_dhcp_config_header(): void
    {
        dd();
    }
}
