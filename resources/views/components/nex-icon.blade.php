@props([
    'size' => 'md', // xs, sm, md, lg, xl
    'variant' => 'amber', // amber, purple, indigo, emerald
    'animate' => true,
])

@php
    $sizes = [
        'xs' => 'size-4',
        'sm' => 'size-6',
        'md' => 'size-10',
        'lg' => 'size-16',
        'xl' => 'size-24',
        '2xl' => 'size-32',
    ];

    $colors = [
        'amber' => 'text-amber-500 fill-amber-500/20 stroke-amber-500',
        'purple' => 'text-purple-500 fill-purple-500/20 stroke-purple-500',
        'indigo' => 'text-indigo-500 fill-indigo-500/20 stroke-indigo-500',
        'emerald' => 'text-emerald-500 fill-emerald-500/20 stroke-emerald-500',
    ];

    $colorClass = $colors[$variant] ?? $colors['amber'];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

<div {{ $attributes->merge(['class' => "relative inline-block $sizeClass $colorClass"]) }}>
    <!-- Magical Pulse Aura -->
    @if($animate)
        <div class="absolute inset-0 rounded-full blur-xl opacity-40 animate-pulse bg-current"></div>
    @endif

    <svg viewBox="0 0 100 100" class="relative z-10 w-full h-full drop-shadow-[0_0_8px_rgba(var(--color-current),0.5)]">
        <defs>
            <linearGradient id="nex-gradient-{{ $variant }}" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" style="stop-color:currentColor;stop-opacity:1" />
                <stop offset="100%" style="stop-color:currentColor;stop-opacity:0.6" />
            </linearGradient>
            
            <filter id="glow-{{ $variant }}">
                <feGaussianBlur stdDeviation="1.5" result="blur" />
                <feComposite in="SourceGraphic" in2="blur" operator="over" />
            </filter>
        </defs>

        <!-- Heptagonal Outer Frame -->
        <path d="M50 5 L89 24 L98 67 L72 95 L28 95 L2 67 L11 24 Z" 
              fill="none" 
              stroke="currentColor" 
              stroke-width="2.5" 
              stroke-linejoin="round"
              class="@if($animate) animate-[spin_20s_linear_infinite] @endif opacity-80" 
              style="transform-origin: center;" />

        <!-- Heptagonal Inner Frame -->
        <path d="M50 15 L78 29 L86 63 L65 85 L35 85 L14 63 L22 29 Z" 
              fill="url(#nex-gradient-{{ $variant }})" 
              stroke="currentColor" 
              stroke-width="1.5" 
              stroke-linejoin="round"
              class="@if($animate) animate-[spin_15s_linear_infinite_reverse] @endif opacity-40" 
              style="transform-origin: center;" />

        <!-- Central "N" Rune -->
        <g class="drop-shadow-sm" filter="url(#glow-{{ $variant }})">
            <path d="M35 30 V70" stroke="currentColor" stroke-width="6" stroke-linecap="round" />
            <path d="M65 30 V70" stroke="currentColor" stroke-width="6" stroke-linecap="round" />
            <path d="M35 30 L65 70" stroke="currentColor" stroke-width="6" stroke-linecap="round" />
        </g>

        <!-- Arcane Runes (Simple strokes at vertices) -->
        <g stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity="0.9">
            <line x1="50" y1="5" x2="50" y2="12" />
            <line x1="89" y1="24" x2="82" y2="28" />
            <line x1="98" y1="67" x2="90" y2="67" />
            <line x1="72" y1="95" x2="68" y2="88" />
            <line x1="28" y1="95" x2="32" y2="88" />
            <line x1="2" y1="67" x2="10" y2="67" />
            <line x1="11" y1="24" x2="18" y2="28" />
        </g>
    </svg>
</div>

<style>
    @keyframes spin-slow {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
</style>
