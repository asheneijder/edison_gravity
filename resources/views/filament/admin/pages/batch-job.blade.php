<x-filament-panels::page>
    <form wire:submit.prevent="cleanLogs">
        {{ $this->form }}
    </form>
</x-filament-panels::page>
