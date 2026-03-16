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

    <!-- WebGL Background -->
    <canvas id="nexus-canvas"
        class="fixed inset-0 z-0 pointer-events-none w-full h-full opacity-60 dark:opacity-100 transition-opacity duration-500"></canvas>

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
                        class="text-sm font-medium text-stone-600 dark:text-stone-300 hover:text-stone-900 dark:hover:text-white transition-colors">{{ __('Dashboard') }}</a>
                @else
                    <div class="flex items-center gap-2">
                        <x-theme-toggle />
                        <a href="{{ route('login') }}"
                            class="text-sm font-medium text-stone-600 dark:text-stone-300 hover:text-stone-900 dark:hover:text-white transition-colors">{{ __('Log in') }}</a>
                    </div>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                            class="text-sm font-medium px-4 py-2 rounded-full bg-amber-600 hover:bg-amber-500 text-white transition-all shadow-lg hover:shadow-amber-500/25">{{ __('Get Started') }}</a>
                    @endif
                @endauth
            @endif
        </div>
    </nav>

    <!-- Hero Section -->
    <main class="relative z-10 flex flex-col items-center justify-center min-h-[calc(100vh-200px)] text-center px-4">
        <div class="animate-float">
            <h1 class="text-5xl md:text-7xl lg:text-8xl font-bold tracking-tighter text-stone-900 dark:text-white mb-6">
                {{ __('Forge Your') }} <span
                    class="text-transparent bg-clip-text bg-gradient-to-r from-amber-500 to-orange-600 text-glow">{{ __('Legend') }}</span>
            </h1>
        </div>

        <p
            class="max-w-2xl text-lg md:text-xl text-stone-600 dark:text-stone-400 mb-10 leading-relaxed md:leading-relaxed">
            {{ __('The next-generation RPG Engine powered by AI.') }} <br class="hidden md:block" />
            {{ __('Play epic campaigns without depending on a Game Master. Nexus acts as your relentless narrative GM for solo or group play.') }}
        </p>

        <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto">
            @if (Route::has('register'))
                <a href="{{ route('register') }}"
                    class="group relative px-8 py-3 rounded-full bg-gradient-to-r from-amber-600 to-orange-600 text-white font-semibold text-lg transition-transform hover:scale-105 hover:shadow-[0_0_40px_-10px_rgba(245,158,11,0.5)]">
                    <span
                        class="absolute inset-0 rounded-full bg-white/20 group-hover:bg-white/30 transition-colors"></span>
                    <span class="relative">{{ __('Start Your Journey') }}</span>
                </a>
            @endif
            <a href="#features"
                class="px-8 py-3 rounded-full bg-white/50 dark:bg-stone-800/50 hover:bg-white/80 dark:hover:bg-stone-800 text-stone-700 dark:text-stone-200 font-medium text-lg border border-stone-200 dark:border-stone-700 hover:border-stone-300 dark:hover:border-stone-600 transition-all backdrop-blur-sm">
                {{ __('Explore Features') }}
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

    <!-- Features Section / The Core -->
    <section id="features"
        class="relative z-10 w-full bg-stone-50 dark:bg-stone-950 border-y border-stone-200 dark:border-white/5 py-32">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-amber-500 font-semibold tracking-wide uppercase text-sm mb-3">{{ __('The Experience') }}
                </h2>
                <h3 class="text-4xl md:text-5xl font-bold text-stone-900 dark:text-white">{{ __('The Core.') }}</h3>
                <p class="mt-4 text-stone-600 dark:text-stone-400 max-w-2xl mx-auto">
                    {{ __('Sell the idea of the perfect rental GM, available 24/7 without organizational headaches.') }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Living World -->
                <div
                    class="bg-white/50 dark:bg-stone-900/50 backdrop-blur-md border border-stone-200 dark:border-stone-800 p-8 rounded-3xl transition-all duration-500 hover:-translate-y-2 hover:shadow-[0_20px_40px_-15px_rgba(245,158,11,0.15)] group">
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-stone-100 to-white dark:from-stone-800 dark:to-stone-900 rounded-2xl shadow-inner flex items-center justify-center mb-8 text-amber-600 dark:text-amber-500 group-hover:scale-110 transition-transform duration-500 border border-stone-200/50 dark:border-stone-700/50">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor"
                            class="size-7 group-hover:drop-shadow-[0_0_8px_rgba(245,158,11,0.5)] transition-all">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-stone-900 dark:text-white mb-3 tracking-tight">
                        {{ __('Living World') }}
                    </h3>
                    <p class="text-stone-600 dark:text-stone-400 leading-relaxed text-sm">
                        {{ __('Organize epic sessions. The Nexus AI Orchestrator remembers all NPCs, towns, and group choices, keeping the world coherent for months.') }}
                    </p>
                </div>

                <!-- Fair GM -->
                <div
                    class="bg-white/50 dark:bg-stone-900/50 backdrop-blur-md border border-stone-200 dark:border-stone-800 p-8 rounded-3xl transition-all duration-500 hover:-translate-y-2 hover:shadow-[0_20px_40px_-15px_rgba(245,158,11,0.15)] group">
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-stone-100 to-white dark:from-stone-800 dark:to-stone-900 rounded-2xl shadow-inner flex items-center justify-center mb-8 text-amber-600 dark:text-amber-500 group-hover:scale-110 transition-transform duration-500 border border-stone-200/50 dark:border-stone-700/50">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor"
                            class="size-7 group-hover:drop-shadow-[0_0_8px_rgba(245,158,11,0.5)] transition-all">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-stone-900 dark:text-white mb-3 tracking-tight">
                        {{ __('Impartial GM') }}
                    </h3>
                    <p class="text-stone-600 dark:text-stone-400 leading-relaxed text-sm">
                        {{ __("Real rolls, real consequences. The AI doesn't cheat to save you, nor to kill you. The narrative adapts to the rolls logically and impartially.") }}
                    </p>
                </div>

                <!-- Intuitive Interface -->
                <div
                    class="bg-white/50 dark:bg-stone-900/50 backdrop-blur-md border border-stone-200 dark:border-stone-800 p-8 rounded-3xl transition-all duration-500 hover:-translate-y-2 hover:shadow-[0_20px_40px_-15px_rgba(245,158,11,0.15)] group relative overflow-hidden">
                    <div
                        class="absolute -right-4 -top-4 w-24 h-24 bg-amber-500/10 rounded-full blur-2xl group-hover:bg-amber-500/20 transition-colors duration-500">
                    </div>
                    <div class="relative">
                        <div
                            class="w-14 h-14 bg-gradient-to-br from-stone-100 to-white dark:from-stone-800 dark:to-stone-900 rounded-2xl shadow-inner flex items-center justify-center mb-8 text-amber-600 dark:text-amber-500 group-hover:scale-110 transition-transform duration-500 border border-stone-200/50 dark:border-stone-700/50">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor"
                                class="size-7 group-hover:drop-shadow-[0_0_8px_rgba(245,158,11,0.5)] transition-all">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-stone-900 dark:text-white mb-3 tracking-tight">
                            {{ __('Intuitive Interface') }}
                        </h3>
                        <p class="text-stone-600 dark:text-stone-400 leading-relaxed text-sm">
                            {{ __('No more complex VTT interfaces. Focus on the narrative and let the automated Nexus panels manage inventory, sheets, and progression.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-16 text-center">
            @if (Route::has('register'))
                <a href="{{ route('register') }}"
                    class="inline-flex items-center gap-2 text-amber-600 dark:text-amber-500 font-medium hover:text-amber-700 dark:hover:text-amber-400 transition-colors group">
                    {{ __('Discover all features') }}
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="size-4 group-hover:translate-x-1 transition-transform">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                </a>
            @endif
        </div>
    </section>

    <!-- Absolute Freedom / Wow Section -->
    <section
        class="relative z-10 py-32 w-full bg-stone-100/30 dark:bg-black/20 border-y border-stone-200 dark:border-white/5 backdrop-blur-sm">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div>
                    <h2 class="text-amber-500 font-semibold tracking-wide uppercase text-sm mb-3">
                        {{ __('Absolute Freedom') }}
                    </h2>
                    <h3 class="text-4xl md:text-5xl font-bold text-stone-900 dark:text-white mb-6">
                        {{ __('No Scripts. No Limits.') }}
                    </h3>
                    <p class="text-lg text-stone-600 dark:text-stone-400 mb-8 leading-relaxed">
                        {{ __('Experience true tabletop roleplaying freedom. Talk to anyone, go anywhere, attempt anything. The AI adapts instantly to your wildest ideas, generating consequences, challenging encounters, and deep narratives on the fly.') }}
                    </p>

                    <div class="space-y-6">
                        <div class="flex gap-4">
                            <div
                                class="flex-shrink-0 w-8 h-8 rounded-full bg-amber-500/20 text-amber-600 dark:text-amber-500 flex items-center justify-center font-bold font-mono text-sm border border-amber-500/30">
                                <flux:icon.sparkles class="size-4" />
                            </div>
                            <div>
                                <h4 class="text-stone-900 dark:text-white font-semibold">
                                    {{ __('Infinite Possibilities') }}
                                </h4>
                                <p class="text-stone-600 dark:text-stone-400 text-sm mt-1">
                                    {{ __('Just tell the GM what you want to do. Your creativity is the only limit.') }}
                                </p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div
                                class="flex-shrink-0 w-8 h-8 rounded-full bg-stone-200 dark:bg-stone-800 text-stone-600 dark:text-stone-400 flex items-center justify-center font-bold font-mono text-sm border border-stone-300 dark:border-stone-700">
                                <flux:icon.arrows-right-left class="size-4" />
                            </div>
                            <div>
                                <h4 class="text-stone-900 dark:text-white font-semibold">{{ __('Deep Consequences') }}
                                </h4>
                                <p class="text-stone-600 dark:text-stone-400 text-sm mt-1">
                                    {{ __('Every action ripples through the world, affecting characters and towns forever.') }}
                                </p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div
                                class="flex-shrink-0 w-8 h-8 rounded-full bg-stone-200 dark:bg-stone-800 text-stone-600 dark:text-stone-400 flex items-center justify-center font-bold font-mono text-sm border border-stone-300 dark:border-stone-700">
                                <flux:icon.bolt class="size-4" />
                            </div>
                            <div>
                                <h4 class="text-stone-900 dark:text-white font-semibold">{{ __('Epic Moments') }}</h4>
                                <p class="text-stone-600 dark:text-stone-400 text-sm mt-1">
                                    {{ __('Experience cinematic combat and dramatic encounters brought to life by AI.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Epic Narrative Visual Representation -->
                <div
                    class="relative w-full aspect-square md:aspect-[4/3] rounded-3xl bg-stone-900 border border-white/10 overflow-hidden shadow-[0_0_50px_-12px_rgba(245,158,11,0.2)] flex items-center justify-center p-4 sm:p-8">
                    <div class="absolute top-0 right-0 p-8 opacity-5 text-amber-500 pointer-events-none">
                        <flux:icon.sparkles class="w-64 h-64" />
                    </div>

                    <div class="w-full flex flex-col gap-6 relative z-10 font-sans">
                        <!-- Player Action -->
                        <div class="flex items-start gap-4 transform transition-all hover:scale-[1.02] duration-300">
                            <div
                                class="flex-shrink-0 w-10 h-10 rounded-full border border-amber-500/30 flex items-center justify-center bg-amber-500/10 text-amber-500 shadow-[0_0_15px_rgba(245,158,11,0.2)]">
                                <flux:icon.user class="size-5" />
                            </div>
                            <div
                                class="flex-1 bg-stone-800/80 backdrop-blur-sm rounded-2xl rounded-tl-none p-4 sm:p-5 border border-white/5 shadow-lg">
                                <p class="text-stone-300 text-sm sm:text-base leading-relaxed">
                                    {{ __('"I want to kick the blazing brazier directly into the goblin leader\'s face, grab the chandelier rope, and swing across the chasm!"') }}
                                </p>
                            </div>
                        </div>

                        <!-- AI GM Response -->
                        <div
                            class="flex items-start gap-4 flex-row-reverse transform transition-all hover:scale-[1.02] duration-300">
                            <div
                                class="flex-shrink-0 w-10 h-10 rounded-full border border-purple-500/30 flex items-center justify-center bg-purple-500/10 text-purple-400 shadow-[0_0_15px_rgba(168,85,247,0.2)]">
                                <flux:icon.bolt class="size-5" />
                            </div>
                            <div
                                class="flex-1 bg-stone-950/80 backdrop-blur-sm rounded-2xl rounded-tr-none p-4 sm:p-5 border border-purple-500/20 shadow-lg relative overflow-hidden">
                                <div
                                    class="absolute inset-0 bg-gradient-to-br from-purple-500/5 to-transparent pointer-events-none">
                                </div>
                                <div class="relative z-10">
                                    <div
                                        class="flex items-center gap-2 mb-3 text-purple-400 text-xs font-bold uppercase tracking-wider">
                                        <flux:icon.check-circle class="size-4" />
                                        {{ __('Acrobatics Check: Success (18)') }}
                                    </div>
                                    <p class="text-stone-300 text-sm sm:text-base leading-relaxed">
                                        {{ __('The brazier shatters, engulfing the leader in embers. You leap, catching the wrought-iron chandelier. It groans under your weight as you swing over the yawning abyss, landing gracefully on the far ledge. The remaining goblins stare in stunned silence.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA Banner -->
    <section class="relative z-10 w-full  py-32 px-6">
        <div
            class="max-w-5xl mx-auto bg-gradient-to-br from-stone-900 to-black rounded-[3rem] p-12 md:p-20 text-center border border-stone-800 shadow-2xl relative overflow-hidden group">

            <div
                class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMSIgY3k9IjEiIHI9IjEiIGZpbGw9InJnYmEoMjU1LCAyNTUsIDI1NSwgMC4wNSkiLz48L3N2Zz4=')] opacity-50">
            </div>

            <div
                class="absolute -top-40 -right-40 w-96 h-96 bg-amber-500/20 blur-[100px] rounded-full group-hover:bg-amber-500/30 group-hover:scale-110 transition-all duration-700">
            </div>

            <div class="relative z-10">
                <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6 tracking-tight">
                    {{ __('Create your Account Today.') }}
                </h2>
                <p class="text-stone-400 text-lg md:text-xl max-w-2xl mx-auto mb-10">
                    {{ __('Nexus is built on the V12 technologies of the Laravel ecosystem. Ready for massive productivity using the TALL stack.') }}
                </p>

                @if (Route::has('register'))
                    <a href="{{ route('register') }}"
                        class="inline-block relative px-10 py-4 rounded-full bg-white text-stone-900 font-bold text-lg transition-transform hover:scale-105 shadow-[0_0_20px_rgba(255,255,255,0.1)] hover:shadow-[0_0_30px_rgba(255,255,255,0.2)]">
                        {{ __('Start for free') }}
                    </a>
                @endif
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer
        class="relative z-10 py-10 bg-stone-50 dark:bg-stone-950 text-center border-t border-stone-200 dark:border-stone-900/50 mt-12 pb-24">
        <div class="flex justify-center gap-6 mb-4 text-sm font-medium">
            <a href="{{ route('docs') }}"
                class="text-stone-500 hover:text-amber-600 dark:text-stone-400 dark:hover:text-amber-500 transition-colors">{{ __('Documentation') }}</a>
            <a href="{{ route('docs', 'terms') }}"
                class="text-stone-500 hover:text-amber-600 dark:text-stone-400 dark:hover:text-amber-500 transition-colors">{{ __('Terms of Service') }}</a>
            <a href="{{ route('docs', 'privacy') }}"
                class="text-stone-500 hover:text-amber-600 dark:text-stone-400 dark:hover:text-amber-500 transition-colors">{{ __('Privacy Policy') }}</a>
        </div>
        <p class="text-stone-500 dark:text-stone-600 text-sm">
            &copy; {{ date('Y') }} Nexus RPG Engine. {{ __('All rights reserved.') }}
        </p>
    </footer>

    @fluxScripts

    <!-- WebGL Background Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const canvas = document.getElementById('nexus-canvas');
            const gl = canvas.getContext('webgl') || canvas.getContext('experimental-webgl');
            if (!gl) {
                console.error("WebGL not supported");
                return;
            }

            const vsSource = `
                attribute vec2 a_position;
                void main() {
                    gl_Position = vec4(a_position, 0.0, 1.0);
                }
            `;

            const fsSource = `
                precision highp float;
                uniform vec2 u_resolution;
                uniform float u_time;
                uniform vec2 u_mouse;
                uniform float u_lightMode;

                float hash(vec3 p) {
                    p = fract(p * 0.3183099 + 0.1);
                    p *= 17.0;
                    return fract(p.x * p.y * p.z * (p.x + p.y + p.z));
                }

                float noise(in vec3 x) {
                    vec3 i = floor(x);
                    vec3 f = fract(x);
                    f = f * f * (3.0 - 2.0 * f);
                    return mix(mix(mix(hash(i + vec3(0.0,0.0,0.0)), hash(i + vec3(1.0,0.0,0.0)), f.x),
                                   mix(hash(i + vec3(0.0,1.0,0.0)), hash(i + vec3(1.0,1.0,0.0)), f.x), f.y),
                               mix(mix(hash(i + vec3(0.0,0.0,1.0)), hash(i + vec3(1.0,0.0,1.0)), f.x),
                                   mix(hash(i + vec3(0.0,1.0,1.0)), hash(i + vec3(1.0,1.0,1.0)), f.x), f.y), f.z);
                }

                float fbm(vec3 x) {
                    float v = 0.0;
                    float a = 0.5;
                    vec3 shift = vec3(100.0);
                    for (int i = 0; i < 5; ++i) {
                        v += a * noise(x);
                        x = x * 2.0 + shift;
                        a *= 0.5;
                    }
                    return v;
                }

                void main() {
                    vec2 st = gl_FragCoord.xy / u_resolution.xy;
                    st.x *= u_resolution.x / u_resolution.y;
                    
                    vec2 mouse = u_mouse / u_resolution.xy;
                    mouse.x *= u_resolution.x / u_resolution.y;

                    float mouseDist = length(st - mouse);
                    float mouseEffect = smoothstep(0.4, 0.0, mouseDist);
                    
                    vec3 q = vec3(0.0);
                    vec2 warpedSt = st + (st - mouse) * mouseEffect * 0.08;
                    
                    q.x = fbm(vec3(warpedSt * 3.0, u_time * 0.1));
                    q.y = fbm(vec3(warpedSt * 3.0 + vec2(1.0), u_time * 0.1));
                    
                    vec3 r = vec3(0.0);
                    r.x = fbm(vec3(warpedSt * 2.5 + q.xy + vec2(1.7, 9.2) + 0.15 * u_time, u_time * 0.05));
                    r.y = fbm(vec3(warpedSt * 2.5 + q.xy + vec2(8.3, 2.8) + 0.126 * u_time, u_time * 0.05));
                    
                    float f = fbm(vec3(warpedSt * 2.0 + r.xy * 2.0, u_time * 0.08));
                    
                    float cloudVol = smoothstep(0.1, 0.9, f);
                    
                    vec3 bgDarkBase = vec3(0.03, 0.03, 0.04);
                    vec3 bgDarkCloud = mix(vec3(0.08, 0.06, 0.09), vec3(0.12, 0.11, 0.13), f);
                    
                    vec3 bgLightBase = vec3(0.96, 0.95, 0.94);
                    vec3 bgLightCloud = mix(vec3(0.90, 0.88, 0.86), vec3(0.85, 0.83, 0.82), f);
                    
                    vec3 colorBase = mix(bgDarkBase, bgLightBase, u_lightMode);
                    vec3 colorCloud = mix(bgDarkCloud, bgLightCloud, u_lightMode);
                    
                    vec3 color = mix(colorBase, colorCloud, cloudVol);
                    
                    // Softer edges
                    float edge = abs(f - 0.5);
                    float softEdge = smoothstep(0.18, 0.0, edge);
                    
                    // Slower, barely noticeable neutral/warm light rays
                    float band1 = pow(sin(st.x * 3.0 + st.y * 3.0 - u_time * 0.8) * 0.5 + 0.5, 5.0);
                    float band2 = pow(sin(st.x * -2.0 + st.y * 4.0 - u_time * 0.5) * 0.5 + 0.5, 6.0);
                    
                    float lightMask = softEdge * (band1 + band2);
                    
                    // Very neutral, soft sunlight colors (white to soft warm gray)
                    vec3 sunColor = mix(vec3(0.6, 0.3, 0.8), vec3(1.0, 0.6, 0.1), f);
                    
                    // Extra attenuation for maximum elegance
                    float glowPower = mix(0.7, 0.35, u_lightMode);
                    color += sunColor * lightMask * glowPower;
                    
                    // Subsurface pointer reduced
                    vec3 pointerColor = vec3(0.9, 0.8, 0.7);
                    float sss = smoothstep(0.4, 0.7, f) * mouseEffect;
                    color += pointerColor * sss * mix(0.3, 0.1, u_lightMode);

                    gl_FragColor = vec4(color, 1.0);
                }
            `;

            function compileShader(gl, source, type) {
                const shader = gl.createShader(type);
                gl.shaderSource(shader, source);
                gl.compileShader(shader);
                if (!gl.getShaderParameter(shader, gl.COMPILE_STATUS)) {
                    console.error('Shader parsing error:', gl.getShaderInfoLog(shader));
                    gl.deleteShader(shader);
                    return null;
                }
                return shader;
            }

            const vertexShader = compileShader(gl, vsSource, gl.VERTEX_SHADER);
            const fragmentShader = compileShader(gl, fsSource, gl.FRAGMENT_SHADER);
            const program = gl.createProgram();
            gl.attachShader(program, vertexShader);
            gl.attachShader(program, fragmentShader);
            gl.linkProgram(program);

            if (!gl.getProgramParameter(program, gl.LINK_STATUS)) {
                console.error('Program linking error:', gl.getProgramInfoLog(program));
                return;
            }

            gl.useProgram(program);

            const positionBuffer = gl.createBuffer();
            gl.bindBuffer(gl.ARRAY_BUFFER, positionBuffer);
            const positions = [
                -1.0, 1.0,
                1.0, 1.0,
                -1.0, -1.0,
                1.0, -1.0,
            ];
            gl.bufferData(gl.ARRAY_BUFFER, new Float32Array(positions), gl.STATIC_DRAW);

            const positionLocation = gl.getAttribLocation(program, "a_position");
            gl.enableVertexAttribArray(positionLocation);
            gl.vertexAttribPointer(positionLocation, 2, gl.FLOAT, false, 0, 0);

            const uResolutionLocation = gl.getUniformLocation(program, "u_resolution");
            const uTimeLocation = gl.getUniformLocation(program, "u_time");
            const uMouseLocation = gl.getUniformLocation(program, "u_mouse");
            const uLightModeLocation = gl.getUniformLocation(program, "u_lightMode");

            let mouseX = window.innerWidth / 2, mouseY = window.innerHeight / 2;
            window.addEventListener('mousemove', (e) => {
                mouseX = e.clientX;
                mouseY = window.innerHeight - e.clientY;
            });

            const getLightMode = () => document.documentElement.classList.contains('dark') ? 0.0 : 1.0;
            let currentLightMode = getLightMode();

            const observer = new MutationObserver(() => {
                currentLightMode = getLightMode();
            });
            observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });

            const resize = () => {
                canvas.width = window.innerWidth;
                canvas.height = window.innerHeight;
                gl.viewport(0, 0, canvas.width, canvas.height);
            };
            window.addEventListener('resize', resize);
            resize();

            const startTime = performance.now();

            const render = (time) => {
                gl.uniform2f(uResolutionLocation, canvas.width, canvas.height);
                gl.uniform1f(uTimeLocation, (time - startTime) * 0.001);
                gl.uniform2f(uMouseLocation, mouseX, mouseY);
                gl.uniform1f(uLightModeLocation, currentLightMode);

                gl.drawArrays(gl.TRIANGLE_STRIP, 0, 4);
                requestAnimationFrame(render);
            };

            requestAnimationFrame(render);
        });
    </script>
</body>

</html>