<div>
    <div class="mb-6 flex gap-4">
        <form wire:submit.prevent="$refresh" class="flex gap-2">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Search hosts..." icon="magnifying-glass" />
        </form>
    </div>

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
                <flux:table.row :variant="$host->status->isDisabledInConfig() ? 'danger' : ($host->ssd === 'Yes' ? 'warning' : null)">
                    <flux:table.cell>
                        <flux:link href="{{ route('hosts.edit', $host) }}" wire:navigate>{{ $host->hostname }}</flux:link>
                    </flux:table.cell>
                    <flux:table.cell>{{ $host->mac }}</flux:table.cell>
                    <flux:table.cell>{{ $host->ip }}</flux:table.cell>
                    <flux:table.cell>{{ $host->added_date?->format('d/m/Y') }}</flux:table.cell>
                    <flux:table.cell>{{ $host->addedByName() }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:link href="mailto:{{ $host->owner }}">{{ $host->ownerName() }}</flux:link>
                    </flux:table.cell>
                    <flux:table.cell class="max-w-xs truncate">{{ $host->notes }}</flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

    <flux:text class="mt-4">Total: {{ $hosts->count() }} entries</flux:text>
</div>
