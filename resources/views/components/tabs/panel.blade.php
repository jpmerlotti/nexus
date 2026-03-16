@props(['name'])

<div x-show="activeTab === '{{ $name }}'" 
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 translate-y-2"
     x-transition:enter-end="opacity-100 translate-y-0"
     {{ $attributes->merge(['class' => 'w-full']) }}
     x-cloak
>
    {{ $slot }}
</div>
