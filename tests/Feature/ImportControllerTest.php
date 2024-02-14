<?php

namespace Tests\Feature;

use App\Jobs\Helper\ErrorCacheInterface;
use App\Jobs\ImportDhcpEntriesJob;
use App\Mail\ImportCompleteMail;
use App\Models\DhcpEntry;
use App\Models\Note;
use App\Models\User;
use App\Services\ExportCsvService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImportControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public ?User $user = null;
    public ?Collection $dhcpEntries = null;

    public ?string $filename = null;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);

        $this->makeErrorCache();

        $this->dhcpEntries = DhcpEntry::factory()
            ->count(10)
            ->has(Note::factory()->count(3))
            ->create([
                'added_by' => 'Prof Test Joe Bloggs'
            ]);
        $this->filename = $this->getCsvFile();
        DhcpEntry::truncate();
    }

    public function test_import_route(): void
    {
        $response = $this->get(route('import-csv.index'));
        $response->assertStatus(200);
    }

    public function test_valid_file_is_handled_correctly(): void
    {
        $this->assertDatabaseCount('dhcp_entries', 0);
        $this->assertDatabaseMissing('dhcp_entries', [
            'added_by' => 'Prof Test Joe Bloggs'
        ]);

        $filePath = Storage::path($this->filename);
        $fileType = mime_content_type($filePath);

        $this->assertFileExists($filePath);
        $this->assertFileIsReadable($filePath);
        $this->assertEquals('text/csv', $fileType);

        $file = new UploadedFile(
            $filePath,
            $this->filename,
            $fileType,
            null,
            true
        );

        $response = $this->post(route('import'), [
            'upload' => $file
        ]);
        $response->assertStatus(200);

        $this->assertDatabaseCount('dhcp_entries', 10);
        $this->assertDatabaseHas('dhcp_entries', [
            'added_by' => 'Prof Test Joe Bloggs'
        ]);

        Storage::delete($this->filename);
    }

    public function test_invalid_file_is_handled_correctly(): void
    {
        $invalidFile = UploadedFile::fake()->image('test-invalid-file');
        $invalidResponse = $this->post(route('import'), [
            'upload' => $invalidFile
        ]);

        $invalidResponse->assertInvalid();
    }

    public function test_import_dispatches_job(): void
    {
        $this->withoutExceptionHandling();

        Queue::fake();
        Queue::assertNothingPushed();

        $filePath = Storage::path($this->filename);
        $fileType = mime_content_type($filePath);

        $file = new UploadedFile(
            $filePath,
            $this->filename,
            $fileType,
            null,
            true
        );

        $this->post(route('import'), [
            'upload' => $file
        ]);

        Queue::assertPushed(ImportDhcpEntriesJob::class);
    }

    public function test_job_imports_dhcp_entries_successfully_and_mail_is_sent_without_errors(): void
    {
        $this->withoutExceptionHandling();

        Mail::fake();
        Mail::assertNothingSent();

        $filePath = Storage::path($this->filename);
        $fileContents = $this->getDataFromCsv($filePath);

        (new ImportDhcpEntriesJob($fileContents, $this->user->email))->handle();

        Mail::assertQueued(ImportCompleteMail::class, function ($mail) {
            return $mail->hasTo($this->user->email) && count($mail->errors) == 0;
        });

        $this->assertDatabaseCount('dhcp_entries', 10);
        $this->assertDatabaseHas('dhcp_entries', [
            'added_by' => 'Prof Test Joe Bloggs'
        ]);
    }

    public function test_job_batch_is_partially_successful_and_mail_is_sent_with_errors(): void
    {
        $this->withoutExceptionHandling();

        Mail::fake();
        Mail::assertNothingSent();

        // Add duplicate DHCP entry which will fail job validation
        $dataProperties = $this->returnCsvDataProperties();
        $filePath = (new ExportCsvService([$this->dhcpEntries->first()], $this->filename, [], $dataProperties))->appendCsvDataToFile($this->filename);
        $newFileContents = $this->getDataFromCsv($filePath);

        ImportDhcpEntriesJob::dispatch($newFileContents, $this->user->email);

        Mail::assertQueued(ImportCompleteMail::class, function ($mail) {
            return $mail->hasTo($this->user->email) && count($mail->errors) == 8;
        });

        $this->assertDatabaseCount('dhcp_entries', 9);
        $this->assertDatabaseHas('dhcp_entries', [
            'added_by' => 'Prof Test Joe Bloggs'
        ]);
    }

    public function test_job_batch_fails_and_mail_is_sent_with_errors(): void
    {
        $this->withoutExceptionHandling();

        Mail::fake();
        Mail::assertNothingSent();

        $this->assertDatabaseCount('dhcp_entries', 0);

        $dataHeaders = $this->returnCsvDataHeaders();
        $dataProperties = $this->returnCsvDataProperties();
        // Initial CSV file with 1 DHCP entry
        (new ExportCsvService([$this->dhcpEntries->first()], $this->filename, $dataHeaders, $dataProperties))->writeCsvDataToFile($this->filename);
        // CSV file with duplicate of initial DHCP entry
        $filePath = (new ExportCsvService([$this->dhcpEntries->first()], $this->filename, [], $dataProperties))->appendCsvDataToFile($this->filename);
        $fileContents = $this->getDataFromCsv($filePath);

        ImportDhcpEntriesJob::dispatch($fileContents, $this->user->email);

        Mail::assertQueued(ImportCompleteMail::class, function ($mail) {
            return $mail->hasTo($this->user->email) && count($mail->errors) == 8;
        });

        $this->assertDatabaseCount('dhcp_entries', 0);
    }

    private function getDataFromCsv(string $filePath): array
    {
        $stream = fopen($filePath, 'r');

        while(($row = fgetcsv($stream)) !== false) {
            $data[] = $row;
        }
        fclose($stream);

        // Remove header element from dhcp data if exists
        if ($data[0][0] == "ID") {
            array_shift($data);
        }

        return $data;
    }

    private function returnCsvDataHeaders(): array
    {
        return [
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
    }

    private function returnCsvDataProperties(): array
    {
        $properties = array_keys($this->dhcpEntries->first()->getAttributes());
        $properties[] = 'notes';

        return $properties;
    }

    private function getCsvFile(): ?string
    {
        $dataHeaders = $this->returnCsvDataHeaders();
        $dataProperties = $this->returnCsvDataProperties();
        $exportCsvService = new ExportCsvService($this->dhcpEntries, 'dhcp-entries', $dataHeaders, $dataProperties);

        $tempFileName = 'testfile-' . Carbon::now()->toDateString() . '-' . Carbon::now()->toTimeString() . '.csv';
        $exportCsvService->writeCsvDataToFile($tempFileName);

        return $tempFileName;
    }

    private function makeErrorCache(string $cacheKey = 'test-cache')
    {
        App::singleton(ErrorCacheInterface::class, function () use ($cacheKey) {
            return new FakeErrorCache($cacheKey);
        });
    }
}

class FakeErrorCache implements ErrorCacheInterface
{
    public array $errors = [];

    public function get()
    {
        return $this->errors;
    }

    public function add(string $message): void
    {
        $this->errors[] = $message;
    }

    public function delete(): void
    {
        // TODO: Implement delete() method.
    }
}
