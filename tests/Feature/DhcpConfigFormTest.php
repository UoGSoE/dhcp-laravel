<?php

namespace Tests\Feature;

use App\Livewire\DhcpConfigForm;
use App\Models\DhcpConfig;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class DhcpConfigFormTest extends TestCase
{
    use RefreshDatabase;

    public ?User $user = null;
    public ?DhcpConfig $dhcpConfig = null;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_livewire_component_is_rendered(): void
    {
        $response = $this->actingAs($this->user)->get(route('dhcp-config'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(DhcpConfigForm::class);
    }

    public function test_data_is_retrieved_if_model_exists(): void
    {
        $this->dhcpConfig = DhcpConfig::factory()->create([
            'header' => 'Lorem ipsum DHCP Header',
            'subnets' => 'Lorem ipsum DHCP Subnets',
            'groups' => 'Lorem ipsum DHCP Groups',
            'footer' => 'Lorem ipsum DHCP Footer'
        ]);

        $this->assertDatabaseCount('dhcp_config', 1);
        $this->assertDatabaseHas('dhcp_config', [
            'header' => 'Lorem ipsum DHCP Header',
            'subnets' => 'Lorem ipsum DHCP Subnets',
            'groups' => 'Lorem ipsum DHCP Groups',
            'footer' => 'Lorem ipsum DHCP Footer'
        ]);

        Livewire::actingAs($this->user)
            ->test(DhcpConfigForm::class)
            ->assertSet('header', $this->dhcpConfig->header)
            ->assertSet('subnets', $this->dhcpConfig->subnets)
            ->assertSet('groups', $this->dhcpConfig->groups)
            ->assertSet('footer', $this->dhcpConfig->footer);
    }

    public function test_data_is_null_if_model_does_not_exist(): void
    {
        Livewire::actingAs($this->user)
            ->test(DhcpConfigForm::class)
            ->assertSet('header', null)
            ->assertSet('subnets', null)
            ->assertSet('groups', null)
            ->assertSet('footer', null);
    }

    public function test_save_dhcp_config_header(): void
    {
        $this->assertDatabaseEmpty('dhcp_config');

        Livewire::actingAs($this->user)
            ->test(DhcpConfigForm::class)
            ->set('header', 'This is a test DHCP Header')
            ->call('saveDhcpConfig');

        $this->assertDatabaseCount('dhcp_config', 1);
        $this->assertDatabaseHas('dhcp_config', [
            'header' => 'This is a test DHCP Header'
        ]);
    }
}
