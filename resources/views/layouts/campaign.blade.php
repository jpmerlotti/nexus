<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body
    class="h-screen w-screen overflow-hidden bg-stone-50 dark:bg-stone-950 text-stone-800 dark:text-stone-200 antialiased selection:bg-amber-500 selection:text-stone-900">

    @livewire('notifications')

    {{ $slot }}

    @fluxScripts
    @filamentScripts
</body>

</html>