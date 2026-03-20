<?php

namespace App\Console\Commands;

use App\Models\DhcpSection;
use App\Models\Host;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

#[Signature('dhcp:migrate-data {--connection=legacy : The database connection name for the old database}')]
#[Description('Import hosts and sections from the legacy DHCP database')]
class MigrateDataCommand extends Command
{
    public function handle(): int
    {
        $connection = $this->option('connection');

        $this->info("Importing from connection: {$connection}");

        $this->importSections($connection);
        $this->importHosts($connection);

        $this->info('Migration complete.');

        return self::SUCCESS;
    }

    private function importSections(string $connection): void
    {
        $rows = DB::connection($connection)->table('eng-dhcp-sections')->get();

        $this->info("Importing {$rows->count()} sections...");

        foreach ($rows as $row) {
            DhcpSection::updateOrCreate(
                ['section' => $row->Section],
                ['body' => $row->Body ?? ''],
            );
        }

        $this->info('Sections imported.');
    }

    private function importHosts(string $connection): void
    {
        $total = DB::connection($connection)->table('eng-dhcp-pool')->count();
        $this->info("Importing {$total} hosts...");

        $imported = 0;
        $warnings = 0;

        DB::connection($connection)
            ->table('eng-dhcp-pool')
            ->orderBy('id')
            ->chunk(500, function ($rows) use (&$imported, &$warnings) {
                foreach ($rows as $row) {
                    $mac = Host::normaliseMac($row->MAC);

                    if ($mac === $row->MAC && strlen(preg_replace('/[^a-fA-F0-9]/', '', $row->MAC ?? '')) !== 12) {
                        Log::warning("Host {$row->id}: MAC failed normalisation, importing raw value", [
                            'original_mac' => $row->MAC,
                        ]);
                        $warnings++;
                    }

                    Host::withoutEvents(function () use ($row, $mac) {
                        $host = Host::find($row->id) ?? new Host;
                        $host->forceFill([
                            'id' => $row->id,
                            'hostname' => $row->Hostname,
                            'mac' => $mac,
                            'ip' => $row->IP ?: null,
                            'added_by' => $row->AddedBy ?? 'unknown',
                            'owner' => $row->Owner ?? 'unknown@glasgow.ac.uk',
                            'added_date' => $row->AddedDate,
                            'wireless' => $row->Wireless ?? 'Yes',
                            'status' => $row->Status ?? 'Enabled',
                            'notes' => $row->Notes,
                            'ssd' => $row->SSD ?: 'No',
                            'last_updated' => $row->LastUpdated,
                        ])->save();
                    });

                    $imported++;
                }
            });

        $this->info("Imported {$imported} hosts ({$warnings} warnings).");
    }
}
