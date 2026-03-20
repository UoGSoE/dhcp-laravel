<div>
    <flux:heading size="xl" level="1">{{ $host ? 'Edit Host' : 'Create Host' }}</flux:heading>

    <form wire:submit="save" class="mt-6 space-y-4 max-w-lg">
        <flux:input label="MAC address" wire:model="mac" placeholder="Colon or dash separated" required />
        <flux:input label="Owner" type="email" wire:model="owner" placeholder="Owner/contact email" required />
        <flux:input label="Fixed IP" wire:model="ip" placeholder="Leave blank for pool" />
        <flux:input label="Hostname" wire:model="hostname" placeholder="Only for fixed-IP machines" />

        <flux:radio.group label="Status" wire:model="status">
            <flux:radio value="Enabled" label="Enabled" />
            <flux:radio value="Disabled" label="Disabled" />
        </flux:radio.group>

        <flux:radio.group label="SSD?" wire:model="ssd">
            <flux:radio value="Yes" label="Yes" />
            <flux:radio value="No" label="No" />
        </flux:radio.group>

        <flux:textarea label="Notes" wire:model="notes" rows="5" />

        <div class="flex gap-2">
            <flux:button type="submit" variant="primary">Save</flux:button>
            <flux:button href="{{ route('home') }}" wire:navigate>Cancel</flux:button>
            @if ($host)
                <flux:button wire:click="delete" wire:confirm="Are you sure you want to delete this host?" variant="danger">Delete</flux:button>
            @endif
        </div>
    </form>
</div>
