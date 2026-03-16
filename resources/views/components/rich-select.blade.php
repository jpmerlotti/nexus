@props(['options' => [], 'placeholder' => 'Select an option', 'label' => null])

<flux:field {{ $attributes->except('wire:model') }}>
    @if($label)
        <flux:label class="mb-3">{{ $label }}</flux:label>
    @endif

    <div x-data="{
            open: false,
            search: '',
            value: @entangle($attributes->wire('model')),
            get selectedOption() {
                if (!this.value) return null;
                return this.options.find(o => o.value == this.value);
            },
            get filteredOptions() {
                if (this.search === '') return this.options;
                return this.options.filter(o => {
                    const titleMatch = o.title && o.title.toLowerCase().includes(this.search.toLowerCase());
                    const descMatch = o.description && o.description.toLowerCase().includes(this.search.toLowerCase());
                    return titleMatch || descMatch;
                });
            },
            options: @js($options)
        }" class="relative w-full" @click.away="open = false" @keydown.escape="open = false">

        <!-- Trigger Button -->
        <button type="button" @click="open = !open"
            class="w-full text-left px-4 py-2.5 bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-700 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500 flex justify-between items-center transition-colors min-h-[46px]">

            <div class="flex-1 overflow-hidden pr-4">
                <template x-if="selectedOption">
                    <div class="flex flex-col">
                        <span class="font-bold text-sm text-stone-900 dark:text-white"
                            x-text="selectedOption.title"></span>
                        <span class="text-xs text-stone-500 dark:text-stone-400 truncate"
                            x-text="selectedOption.description"></span>
                    </div>
                </template>
                <template x-if="!selectedOption">
                    <span class="text-stone-500 dark:text-stone-400 text-sm" x-text="'{{ $placeholder }}'"></span>
                </template>
            </div>

            <div class="flex-shrink-0 text-stone-400 transition-transform duration-200"
                :class="open ? 'rotate-180' : ''">
                <flux:icon.chevron-down class="w-4 h-4" />
            </div>
        </button>

        <!-- Dropdown Menu -->
        <div x-cloak x-show="open" x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="transform opacity-0 scale-95 translate-y-[-10px]"
            x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="transform opacity-0 scale-95 translate-y-[-10px]"
            class="absolute z-50 w-full mt-2 bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-700 rounded-xl shadow-2xl overflow-hidden">
            <!-- Search Bar -->
            <div class="p-3 border-b border-stone-100 dark:border-stone-800 bg-stone-50/50 dark:bg-stone-950/50">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-stone-400">
                        <flux:icon.magnifying-glass class="w-4 h-4" />
                    </div>
                    <input x-ref="searchInput" type="text" x-model="search" placeholder="{{ __('Search...') }}"
                        class="w-full pl-9 pr-3 py-2 bg-white dark:bg-black border border-stone-200 dark:border-stone-800 rounded-lg text-sm focus:ring-amber-500 focus:border-amber-500 text-stone-900 dark:text-white placeholder-stone-400 transition-colors">
                </div>
            </div>

            <!-- Options List -->
            <ul class="max-h-64 overflow-y-auto py-2 custom-scrollbar">
                <template x-for="(option, index) in filteredOptions" :key="option.value">
                    <li @click="value = option.value; open = false; search = ''"
                        class="px-4 py-3 cursor-pointer transition-colors flex flex-col group border-b border-stone-50 dark:border-stone-800/50 last:border-0"
                        :class="value == option.value ? 'bg-amber-50 dark:bg-amber-900/10' : 'hover:bg-stone-50 dark:hover:bg-stone-800/30'">
                        <div class="flex justify-between items-start">
                            <span
                                class="font-bold text-sm text-stone-900 dark:text-stone-200 group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors"
                                x-text="option.title"></span>
                            <div x-show="value == option.value" class="text-amber-500 flex-shrink-0 ml-2 mt-0.5">
                                <flux:icon.check class="w-4 h-4" />
                            </div>
                        </div>
                        <span class="text-xs text-stone-500 dark:text-stone-400 mt-1 leading-relaxed"
                            x-text="option.description" x-show="option.description"></span>
                    </li>
                </template>

                <li x-show="filteredOptions.length === 0"
                    class="px-4 py-6 text-sm text-stone-500 text-center flex flex-col items-center justify-center">
                    <flux:icon.magnifying-glass class="w-6 h-6 mb-2 opacity-30" />
                    {{ __('No options found matching your search.') }}
                </li>
            </ul>
        </div>
    </div>
</flux:field>