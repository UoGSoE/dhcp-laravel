<div>
    <flux:heading size="xl" level="1">Edit Section: {{ $section->section }}</flux:heading>

    <form wire:submit="save" class="mt-6 max-w-3xl">
        <flux:textarea wire:model="body" rows="20" class="font-mono" />

        <div class="mt-4 flex gap-2">
            <flux:button type="submit" variant="primary">Save</flux:button>
            <flux:button href="{{ route('home') }}" wire:navigate>Cancel</flux:button>
        </div>
    </form>
</div>
