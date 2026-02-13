<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body
    class="min-h-screen bg-stone-50 dark:bg-stone-950 text-stone-800 dark:text-stone-200 antialiased selection:bg-amber-500 selection:text-stone-900">

    <!-- Background Effects -->
    <div class="fixed inset-0 z-0 pointer-events-none overflow-hidden">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-amber-500/10 rounded-full blur-[100px] animate-float"></div>
        <div
            class="absolute bottom-0 right-1/4 w-[500px] h-[500px] bg-orange-600/10 rounded-full blur-[120px] animate-float-delayed">
        </div>
    </div>

    <div class="relative flex min-h-screen flex-col">
        <!-- Header -->
        <header
            class="sticky top-0 z-50 w-full border-b border-stone-200 dark:border-stone-800 bg-stone-50/80 dark:bg-stone-950/80 backdrop-blur-md">
            <div class="container flex h-16 items-center space-x-4 sm:justify-between sm:space-x-0 mx-auto px-4">
                <div class="flex gap-6 md:gap-10">
                    <a class="flex items-center space-x-2" href="/">
                        <x-app-logo-icon class="size-6 text-amber-500" />
                        <span class="inline-block font-bold">Nexus Docs</span>
                    </a>
                </div>
                <div class="flex flex-1 items-center justify-end space-x-4">
                    <nav class="flex items-center gap-2">
                        @auth
                            <x-desktop-user-menu />
                        @else
                            <x-theme-toggle />
                            <flux:button href="{{ route('login') }}" variant="ghost" size="sm">{{ __('Log in') }}
                            </flux:button>
                            @if (Route::has('register'))
                                <flux:button href="{{ route('register') }}" variant="primary" size="sm">{{ __('Register') }}
                                </flux:button>
                            @endif
                        @endauth
                    </nav>
                </div>
            </div>
        </header>

        <div class="container mx-auto max-w-screen-xl px-4 py-8">
            <div class="grid grid-cols-1 md:grid-cols-[240px_1fr] xl:grid-cols-[240px_1fr_240px] gap-10 items-start">

                <!-- Left Sidebar (Navigation) -->
                <aside
                    class="hidden md:block sticky top-24 h-[calc(100vh-8rem)] overflow-y-auto shrink-0 border-r border-stone-200 dark:border-stone-800 pr-6">
                    <div class="prose dark:prose-invert prose-stone prose-sm">
                        {!! $index !!}
                    </div>
                </aside>

                <!-- Main Content -->
                <main class="min-w-0 w-full">
                    <div class="prose dark:prose-invert prose-stone max-w-none">
                        <h1>{{ $title }}</h1>
                        {!! $content !!}
                    </div>
                </main>

                <!-- Right Sidebar (Table of Contents) -->
                <aside
                    class="hidden xl:block sticky top-24 h-[calc(100vh-8rem)] overflow-y-auto shrink-0 border-l border-stone-200 dark:border-stone-800 pl-6 text-sm">
                    @if(isset($toc) && count($toc) > 0)
                        <h4 class="font-medium text-stone-900 dark:text-stone-100 mb-4">{{ __('On this page') }}</h4>
                        <ul class="space-y-2">
                            @foreach($toc as $item)
                                <li class="{{ $item['level'] === 3 ? 'pl-4' : '' }}">
                                    <a href="#{{ $item['id'] }}"
                                        class="text-stone-600 dark:text-stone-400 hover:text-amber-600 dark:hover:text-amber-500 transition-colors block py-0.5">
                                        {{ $item['title'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </aside>

            </div>
        </div>
    </div>
    </div>

    <footer class="py-10 text-center border-t border-stone-200 dark:border-stone-800 mt-auto">
        <div class="flex justify-center gap-6 mb-4 text-sm font-medium">
            <a href="/" class="text-stone-500 hover:text-amber-600 dark:hover:text-amber-500 transition-colors">Home</a>
            <a href="{{ route('docs') }}"
                class="text-stone-500 hover:text-amber-600 dark:hover:text-amber-500 transition-colors">Documentation</a>
            <a href="{{ route('docs', 'terms') }}"
                class="text-stone-500 hover:text-amber-600 dark:hover:text-amber-500 transition-colors">Terms of
                Service</a>
            <a href="{{ route('docs', 'privacy') }}"
                class="text-stone-500 hover:text-amber-600 dark:hover:text-amber-500 transition-colors">Privacy
                Policy</a>
        </div>
        <p class="text-stone-400 dark:text-stone-600 text-sm">
            &copy; {{ date('Y') }} Nexus RPG Engine. All rights reserved.
        </p>
    </footer>
    </div>

    @fluxScripts
</body>

</html>