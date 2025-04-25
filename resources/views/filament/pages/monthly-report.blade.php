<x-filament::page>
    
    <x-filament::tabs>
        <x-filament::tabs.item
            :active="$activeTab === 'income'"
            wire:click="$set('activeTab', 'income')"
        >
           Income
        </x-filament::tabs.item>

        <x-filament::tabs.item
            :active="$activeTab === 'expense'"
            wire:click="$set('activeTab', 'expense')"
        >
           Expense
        </x-filament::tabs.item>
    </x-filament::tabs>

    <!-- Conditional section based on active tab -->
    @if ($activeTab === 'income')
        
       <div>
         <!-- Income Bar Chart Widget -->
         <div class="mt-6">
            @livewire(\App\Filament\Widgets\IncomeBarChartWidget::class, [
                'selectedMonth' => $selectedMonth,
            ])
        </div>
        <div class="mt-6">
            @livewire(\App\Filament\Widgets\IncomeTableWidget::class, [
                'selectedMonth' => $selectedMonth,
            ])
        </div>
       </div>
    @elseif ($activeTab === 'expense')  
        
      <div>
          <!-- Expense Bar Chart Widget -->
          <div class="mt-6">
            @livewire(\App\Filament\Widgets\ExpenseBarChartWidget::class, [
                'selectedMonth' => $selectedMonth,
            ])
        </div>
        <div class="mt-6">
            @livewire(\App\Filament\Widgets\ExpenseTableWidget::class, [
                'selectedMonth' => $selectedMonth,
            ])
        </div>

      </div>
    @endif

   
</x-filament::page>
