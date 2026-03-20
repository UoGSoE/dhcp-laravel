<div>
    <flux:table>
        <flux:table.columns>
            <flux:table.column>Hostname</flux:table.column>
            <flux:table.column>MAC</flux:table.column>
            <flux:table.column>IP</flux:table.column>
            <flux:table.column>Added Date</flux:table.column>
            <flux:table.column>Added By</flux:table.column>
            <flux:table.column>Owner</flux:table.column>
            <flux:table.column>Notes</flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @foreach ($hosts as $host)
                <flux:table.row>
                    <flux:table.cell>{{ $host->hostname }}</flux:table.cell>
                    <flux:table.cell>{{ $host->mac }}</flux:table.cell>
                    <flux:table.cell>{{ $host->ip }}</flux:table.cell>
                    <flux:table.cell>{{ $host->added_date?->format('d/m/Y') }}</flux:table.cell>
                    <flux:table.cell>{{ $host->addedByName() }}</flux:table.cell>
                    <flux:table.cell>{{ $host->owner }}</flux:table.cell>
                    <flux:table.cell class="max-w-xs truncate">{{ $host->notes }}</flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

    <flux:text class="mt-4">Total: {{ $hosts->count() }} entries</flux:text>
</div>
