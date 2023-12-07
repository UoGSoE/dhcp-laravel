<?php

namespace Tests\Feature;

use App\Livewire\ImportComponent;
use App\Models\DhcpEntry;
use App\Models\Note;
use App\Models\User;
use App\Services\ExportCsvService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\Livewire;
use Tests\TestCase;

class ImportComponentTest extends TestCase
{
    use RefreshDatabase;

    public ?User $user = null;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_route(): void
    {
        $response = $this->get(route('import-csv.index'));
        $response->assertStatus(200);
    }

    public function test_import_from_csv(): void
    {
        $this->assertDatabaseCount('dhcp_entries', 0);
        $this->assertDatabaseMissing('dhcp_entries', [
            'added_by' => 'Prof Test Joe Bloggs'
        ]);

        $filename = $this->getCsvFile();
        $file = Storage::get($filename);

        $testFile = TemporaryUploadedFile::fake()->createWithContent($filename, $file);

        $response = Livewire::test(ImportComponent::class)
            ->set('uploadedCsv', $testFile)
            ->call('import')
            ->assertHasNoErrors();

        $this->assertDatabaseCount('dhcp_entries', 10);
        $this->assertDatabaseHas('dhcp_entries', [
            'added_by' => 'Prof Test Joe Bloggs'
        ]);

        Storage::delete($filename);
    }

    private function getCsvFile(): ?string
    {
        $dhcpEntries = DhcpEntry::factory()
            ->count(10)
            ->has(Note::factory()->count(3))
            ->create([
                'added_by' => 'Prof Test Joe Bloggs'
            ]);

        $dataProperties = array_keys($dhcpEntries->first()->getAttributes());
        $dataProperties[] = 'notes';
        $dataHeaders = [
            'ID',
            'Hostname',
            'Mac Address',
            'IP Address',
            'Owner',
            'Added By',
            'Campus System?',
            'Active?',
            'Imported?',
            'Created At',
            'Updated At',
            'Note (Last Updated)'
        ];

        $exportCsvService = new ExportCsvService($dhcpEntries, 'dhcp-entries', $dataHeaders, $dataProperties);

        $tempFilePath = 'testfile-' . Carbon::now()->toDateString() . '-' . Carbon::now()->toTimeString() . '.csv';
        $exportCsvService->exportCsvToFile($tempFilePath);

        return $tempFilePath;
    }
}
