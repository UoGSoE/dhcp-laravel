<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ImportControllerTest extends TestCase
{
    use RefreshDatabase;

    public ?User $user = null;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_import_from_csv(): void
    {
        $this->actingAs($this->user);

        $response = $this->get(route('import-csv.index'));
        $response->assertStatus(200);
    }
}
