@props(['month'])
<x-filament::input.wrapper>
    <x-filament::input
        type="month" 
        wire:model="selectedMonth" wire:change="updateSelectedMonth($event.target.value)"  max="{{ date('Y-m') }}"
    />
</x-filament::input.wrapper>