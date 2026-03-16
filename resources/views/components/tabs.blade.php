@props(['default' => ''])

<div x-data="{ activeTab: '{{ $default }}' }" {{ $attributes->class(['w-full']) }}>
    @isset($tabs)
        <div
            class="flex space-x-1 bg-stone-100/50 dark:bg-stone-950/50 p-1 rounded-xl border border-stone-200 dark:border-stone-800 w-full sm:w-fit mb-6">
            {{ $tabs }}
        </div>
    @endisset

    <div class="mt-4">
        {{ $slot }}
    </div>
</div>