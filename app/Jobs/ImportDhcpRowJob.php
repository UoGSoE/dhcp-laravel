<?php

namespace App\Jobs;

use App\Jobs\Helper\ErrorCache;
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
use Illuminate\Support\Facades\Log;

class ImportDhcpRowJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private array $dhcpEntry,
//        private string $cacheKey
    )
    {
    }

    public function handle(): void
    {
        if ($this->batch()->cancelled()) {
            return;
        }
        Log::info($this->dhcpEntry['id']);

        DhcpEntry::updateOrCreate([
            'id' => $this->dhcpEntry['id']
        ], Arr::except($this->dhcpEntry, 'note'));


        if ($this->dhcpEntry['note']) {
            Note::updateOrCreate([
                'id' => $this->dhcpEntry['note']['id']
            ], $this->dhcpEntry['note']);
        }
    }

    public function failed($e)
    {
        Log::info("Import DHCP row job failed: {$e->getMessage()} ");
//        $cache = new ErrorCache($this->cacheKey);
//        $cache->add($e->getMessage() . $this->dhcpEntry['id']);
    }
}
