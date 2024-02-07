<?php

namespace App\Jobs;

use App\Jobs\Helper\ErrorCache;
use App\Mail\ImportCompleteMail;
use DateTime;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;
use Throwable;

class ImportDhcpEntriesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 1200;
    public string $emailAddress;
    public array $data;

    public function __construct(
        array $data,
        string $emailAddress

    ) {
        $this->data = $data;
        $this->emailAddress = $emailAddress;
    }

    public function handle()
    {
        Log::info('Starting job: import DHCP entries');

        // Format data as required for dhcp entry model
        $dhcpEntries = [];
        foreach ($this->data as $entry) {
            $dhcpEntries[] = [
                'id' => $entry[0],
                'hostname' => $entry[1],
                'mac_address' => $entry[2],
                'ip_address' => $entry[3] ?: null,
                'owner' => $entry[4],
                'added_by' => $entry[5],
                'is_ssd' => strtolower($entry[6]) === "true",
                'is_active' => strtolower($entry[7]) === "true",
                'is_imported' => true,
                'created_at' => $entry[9],
                'updated_at' => $entry[10],
                'note' => $entry[11] !== "" ? json_decode($entry[11], true) : null,
            ];
        }

        // Validate dhcp entries
        $validator = Validator::make($dhcpEntries, [
            '*.id' => 'required|uuid|distinct',
            '*.hostname' => 'required|string|distinct|unique:dhcp_entries,hostname',
            '*.mac_address' => 'required|mac_address|distinct|unique:dhcp_entries,mac_address',
            '*.ip_address' => 'nullable|ip|distinct|unique:dhcp_entries,ip_address',
            '*.owner' => 'required|string',
            '*.added_by' => 'required|string',
            '*.is_ssd' => 'required|boolean',
            '*.is_active' => 'required|boolean',
            '*.is_imported' => 'required|boolean',
            '*.created_at' => 'required|string',
            '*.updated_at' => 'required|string',
            '*.note' => 'nullable|array'
        ]);

        // Log failed entries and add error to cache
        $cacheKey = 'dhcp-import-' . now()->timestamp;
        if ($validator->fails()) {
            foreach ($validator->errors()->toArray() as $errorKey => $errorMsg) {
                $arrayKey = preg_replace("/([.]+)([a-z0-9]+)/", "", $errorKey);
                $property = preg_replace("/^([^.]+)([.])/", "", $errorKey);

                $failedDhcpEntry = [
                    'dhcpEntryId' => $dhcpEntries[$arrayKey]['id'],
                    'dhcpHostname' => $dhcpEntries[$arrayKey]['hostname'],
                    'property' => $property,
                    'errorMessage' => $errorMsg[0]
                ];

                Log::info("Validation failed for DHCP entry '{$failedDhcpEntry['dhcpEntryId']}' ({$failedDhcpEntry['dhcpHostname']}) : {$failedDhcpEntry['errorMessage']}");

                (new ErrorCache($cacheKey))->add(
                    "Validation failed for DHCP entry '{$failedDhcpEntry['dhcpEntryId']}' ({$failedDhcpEntry['dhcpHostname']}). " .
                    "Property {$failedDhcpEntry['property']} has error: " .
                    "{$failedDhcpEntry['errorMessage']}"
                );
            }
        }

        // Create batch jobs only for the valid entries returned by validator
        $batchJobs = [];
        foreach ($validator->valid() as $validEntry) {
            $batchJobs[] = new ImportDhcpRowJob($validEntry);
        }

        $email = $this->emailAddress;

        // Dispatch batch jobs
        Bus::batch($batchJobs)
            ->allowFailures()
            ->catch(function(Batch $batch, Throwable $e) use ($cacheKey) {
                Log::info("Import DHCP entries: error on batch {$batch->id}: {$e->getMessage()} ");

               (new ErrorCache($cacheKey))->add($e->getMessage());
            })
            ->finally(function() use ($cacheKey, $email) {
                Log::info("Job finished: import DHCP entries");
                $cache = new ErrorCache($cacheKey);
                $errors = $cache->get();
                $cache->delete();
                Mail::to($email)->queue(new ImportCompleteMail($errors));
            })
            ->dispatch();
    }

    public function failed($e)
    {
        Log::info('Import DHCP entries job failed: ' . $e->getMessage());
    }
}
