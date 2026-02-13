<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nexus RPG Engine</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
</head>

<body
    class="font-sans antialiased bg-stone-50 dark:bg-stone-950 text-stone-900 dark:text-stone-200 overflow-x-hidden selection:bg-amber-500 selection:text-stone-900 transition-colors duration-300">

    <!-- Background Effects -->
    <div class="fixed inset-0 z-0 pointer-events-none overflow-hidden">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-amber-500/10 rounded-full blur-[100px] animate-float"></div>
        <div
            class="absolute bottom-0 right-1/4 w-[500px] h-[500px] bg-orange-600/10 rounded-full blur-[120px] animate-float-delayed">
        </div>
    </div>

    <!-- Navigation -->
    <nav class="relative z-50 w-full max-w-7xl mx-auto px-6 py-6 flex justify-between items-center">
        <div class="flex items-center gap-2">
            <x-app-logo-icon class="size-8 md:size-10 text-amber-600 dark:text-amber-500" />
            <span class="text-xl md:text-2xl font-bold tracking-tight text-stone-900 dark:text-white">Nexus</span>
        </div>
        <div class="flex items-center gap-4 md:gap-8">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}"
                        class="text-sm font-medium text-stone-600 dark:text-stone-300 hover:text-stone-900 dark:hover:text-white transition-colors">Dashboard</a>
                @else
                    <div class="flex items-center gap-2">
                        <x-theme-toggle />
                        <a href="{{ route('login') }}"
                            class="text-sm font-medium text-stone-600 dark:text-stone-300 hover:text-stone-900 dark:hover:text-white transition-colors">Log
                            in</a>
                    </div>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                            class="text-sm font-medium px-4 py-2 rounded-full bg-amber-600 hover:bg-amber-500 text-white transition-all shadow-lg hover:shadow-amber-500/25">Get
                            Started</a>
                    @endif
                @endauth
            @endif
        </div>
    </nav>

    <!-- Hero Section -->
    <main class="relative z-10 flex flex-col items-center justify-center min-h-[calc(100vh-200px)] text-center px-4">
        <div class="animate-float">
            <h1 class="text-5xl md:text-7xl lg:text-8xl font-bold tracking-tighter text-stone-900 dark:text-white mb-6">
                Forge Your <span
                    class="text-transparent bg-clip-text bg-gradient-to-r from-amber-500 to-orange-600 text-glow">Legend</span>
            </h1>
        </div>

        <p
            class="max-w-2xl text-lg md:text-xl text-stone-600 dark:text-stone-400 mb-10 leading-relaxed md:leading-relaxed">
            The next-generation RPG Engine powered by AI. <br class="hidden md:block" />
            Build immersive worlds, manage complex campaigns, and unleash your imagination.
        </p>

        <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto">
            @if (Route::has('register'))
                <a href="{{ route('register') }}"
                    class="group relative px-8 py-3 rounded-full bg-gradient-to-r from-amber-600 to-orange-600 text-white font-semibold text-lg transition-transform hover:scale-105 hover:shadow-[0_0_40px_-10px_rgba(245,158,11,0.5)]">
                    <span
                        class="absolute inset-0 rounded-full bg-white/20 group-hover:bg-white/30 transition-colors"></span>
                    <span class="relative">Start Your Journey</span>
                </a>
            @endif
            <a href="#features"
                class="px-8 py-3 rounded-full bg-white/50 dark:bg-stone-800/50 hover:bg-white/80 dark:hover:bg-stone-800 text-stone-700 dark:text-stone-200 font-medium text-lg border border-stone-200 dark:border-stone-700 hover:border-stone-300 dark:hover:border-stone-600 transition-all backdrop-blur-sm">
                Explore Features
            </a>
        </div>

        <!-- Floating Elements -->
        <div
            class="absolute top-1/2 left-10 md:left-20 w-16 h-16 border border-amber-500/30 rounded-lg rotate-12 animate-float-delayed hidden lg:block">
        </div>
        <div
            class="absolute top-1/3 right-10 md:right-20 w-24 h-24 border border-stone-300/50 dark:border-stone-700/50 rounded-full animate-float hidden lg:block">
        </div>
    </main>

    <!-- Features Section -->
    <section id="features" class="relative z-10 py-24 px-6 max-w-7xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Card 1 -->
            <div
                class="bg-white/60 dark:bg-stone-900/40 backdrop-blur-md border border-stone-200 dark:border-stone-800 p-8 rounded-2xl transition-transform hover:-translate-y-2 hover:shadow-xl dark:hover:shadow-black/50 group">
                <div
                    class="w-12 h-12 bg-stone-100 dark:bg-stone-800 rounded-lg flex items-center justify-center mb-6 text-amber-600 dark:text-amber-500 group-hover:scale-110 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-stone-900 dark:text-white mb-3">AI Storytelling</h3>
                <p class="text-stone-600 dark:text-stone-400 leading-relaxed">Let our advanced AI weaving complex
                    narratives, generating
                    NPCs, and creating plot twists on the fly.</p>
            </div>

            <!-- Card 2 -->
            <div
                class="bg-white/60 dark:bg-stone-900/40 backdrop-blur-md border border-stone-200 dark:border-stone-800 p-8 rounded-2xl transition-transform hover:-translate-y-2 hover:shadow-xl dark:hover:shadow-black/50 group">
                <div
                    class="w-12 h-12 bg-stone-100 dark:bg-stone-800 rounded-lg flex items-center justify-center mb-6 text-amber-600 dark:text-amber-500 group-hover:scale-110 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-stone-900 dark:text-white mb-3">Infinite Worlds</h3>
                <p class="text-stone-600 dark:text-stone-400 leading-relaxed">Explore procedurally generated realms or
                    meticulously craft
                    your own with our world-building tools.</p>
            </div>

            <!-- Card 3 -->
            <div
                class="bg-white/60 dark:bg-stone-900/40 backdrop-blur-md border border-stone-200 dark:border-stone-800 p-8 rounded-2xl transition-transform hover:-translate-y-2 hover:shadow-xl dark:hover:shadow-black/50 group">
                <div
                    class="w-12 h-12 bg-stone-100 dark:bg-stone-800 rounded-lg flex items-center justify-center mb-6 text-amber-600 dark:text-amber-500 group-hover:scale-110 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-stone-900 dark:text-white mb-3">Custom Systems</h3>
                <p class="text-stone-600 dark:text-stone-400 leading-relaxed">Adapt the engine to your preferred rule
                    set. From D&D 5e to
                    custom homebrew mechanics.</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="relative z-10 py-10 text-center border-t border-stone-200 dark:border-stone-900/50 mt-12 mb-20">
        <div class="flex justify-center gap-6 mb-4 text-sm font-medium">
            <a href="{{ route('docs') }}"
                class="text-stone-500 hover:text-amber-600 dark:text-stone-400 dark:hover:text-amber-500 transition-colors">Documentation</a>
            <a href="{{ route('docs', 'terms') }}"
                class="text-stone-500 hover:text-amber-600 dark:text-stone-400 dark:hover:text-amber-500 transition-colors">Terms
                of Service</a>
            <a href="{{ route('docs', 'privacy') }}"
                class="text-stone-500 hover:text-amber-600 dark:text-stone-400 dark:hover:text-amber-500 transition-colors">Privacy
                Policy</a>
        </div>
        <p class="text-stone-500 dark:text-stone-600 text-sm">
            &copy; {{ date('Y') }} Nexus RPG Engine. All rights reserved.
        </p>
    </footer>

    @fluxScripts
</body>

</html>