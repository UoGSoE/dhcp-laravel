<div>
    <div class="mb-6 flex items-center gap-4">
        <flux:input wire:model.live.debounce.300ms="search" placeholder="Search hosts..." icon="magnifying-glass" class="flex-1" />

        <flux:dropdown>
            <flux:button icon="arrow-down-tray" variant="ghost">Export</flux:button>
            <flux:menu>
                <flux:menu.item href="{{ route('export.csv') }}" icon="document-text">CSV</flux:menu.item>
                <flux:menu.item href="{{ route('export.json') }}" icon="code-bracket">JSON</flux:menu.item>
            </flux:menu>
        </flux:dropdown>

        <flux:modal.trigger name="host-form">
            <flux:button icon="plus" variant="primary" wire:click="$dispatch('create-host')">New Host</flux:button>
        </flux:modal.trigger>
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
                        <flux:link href="#" wire:click.prevent="$dispatch('edit-host', { id: {{ $host->id }} })">{{ $host->hostname }}</flux:link>
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

    <flux:modal name="host-form" flyout class="md:w-lg">
        <livewire:host-form />
    </flux:modal>
</div>
