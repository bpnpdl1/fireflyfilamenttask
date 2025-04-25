@props(['month'])
<x-filament::input.wrapper>
    <x-filament::input
        type="month" 
        wire:model="selectedMonth"
    />
</x-filament::input.wrapper>