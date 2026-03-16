@props(['name', 'icon' => null])

<button type="button" 
    @click="activeTab = '{{ $name }}'"
    :class="activeTab === '{{ $name }}' 
        ? 'bg-white dark:bg-stone-800 text-amber-600 dark:text-amber-400 shadow-sm border-stone-200 dark:border-stone-700' 
        : 'text-stone-500 dark:text-stone-400 hover:text-stone-700 dark:hover:text-stone-200 border-transparent'"
    {{ $attributes->class(['flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg border transition-all duration-200 focus:outline-none']) }}
>
    @if($icon)
        <flux:icon :name="$icon" variant="micro" class="w-4 h-4" />
    @endif
    
    {{ $slot }}
</button>
