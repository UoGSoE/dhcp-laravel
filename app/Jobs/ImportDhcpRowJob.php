<?php

namespace App\Jobs;

use App\Jobs\Helper\ErrorCache;
use App\Jobs\Helper\ErrorCacheInterface;
use App\Models\DhcpEntry;
use App\Models\Note;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;

class ImportDhcpRowJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private array $dhcpEntry,
        private string $cacheKey
    )
    {
    }

    public function handle(): void
    {
        if ($this->batch()->cancelled()) {
            return;
        }

        Log::info("Import DHCP row job running: {$this->dhcpEntry['id']}");

        DhcpEntry::updateOrCreate([
            'id' => $this->dhcpEntry['id']
        ], Arr::except($this->dhcpEntry, 'note'));


        if ($this->dhcpEntry['note']) {
            Note::updateOrCreate([
                'id' => $this->dhcpEntry['note']['id']
            ], $this->dhcpEntry['note']);
        }
    }

    public function failed($e): void
    {
        Log::info("Import DHCP row job failed: {$e->getMessage()} ");
        App::get(ErrorCacheInterface::class)->add(
            "Import DHCP row job failed for entry '{$this->dhcpEntry['id']}' ($this->dhcpEntry['hostname']}). " .
            "Error: {$e->getMessage()}"
        );
    }
}
