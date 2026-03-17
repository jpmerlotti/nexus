<?php

use App\Models\Post;
use Livewire\Volt\Component;
use Illuminate\Support\Str;

new class extends Component
{
    public Post $post;
    public int $claps = 0;
    public int $userClaps = 0;

    public function mount(Post $post)
    {
        $this->post = $post;
        $this->claps = $post->claps;
        $this->userClaps = session()->get('claps_' . $post->id, 0);
    }

    public function clap()
    {
        if ($this->userClaps >= 50) {
            return;
        }

        $this->post->increment('claps');
        $this->claps++;
        
        $this->userClaps++;
        session()->put('claps_' . $this->post->id, $this->userClaps);
    }
};
?>

<div class="flex items-center gap-4 my-8">
    <button 
        x-data="{ animate: false }"
        @click="$wire.clap(); animate = true; setTimeout(() => animate = false, 300)"
        class="group relative flex h-14 w-14 items-center justify-center rounded-full border border-zinc-200 bg-white shadow-sm transition-all hover:border-zinc-300 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-zinc-700 disabled:opacity-50 disabled:cursor-not-allowed"
        @disabled($userClaps >= 50)
        title="Clap for this post"
    >
        <div :class="animate ? 'scale-125 text-amber-500' : 'scale-100 text-zinc-500 group-hover:text-amber-500'" class="transition-all duration-300 ease-[cubic-bezier(0.34,1.56,0.64,1)]">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.042 21.672 13.684 16.6m0 0-2.51 2.225.569-9.47 5.227 7.917-3.286-.672ZM12 2.25V4.5m5.834.166-1.591 1.591M20.25 10.5H18M7.757 14.743l-1.59 1.59M6 10.5H3.75m4.007-4.243-1.59-1.59" />
            </svg>
        </div>
        
        <span 
            x-show="animate" 
            x-transition:enter="transition-all ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-0 scale-50"
            x-transition:enter-end="opacity-100 -translate-y-12 scale-100"
            x-transition:leave="transition-all ease-in duration-300"
            x-transition:leave-start="opacity-100 -translate-y-12 scale-100"
            x-transition:leave-end="opacity-0 -translate-y-16 scale-110"
            class="absolute -top-2 flex h-8 w-8 items-center justify-center rounded-full bg-amber-500 text-xs font-bold text-white shadow-md pointer-events-none"
            x-cloak
        >
            +1
        </span>
    </button>
    
    <div class="flex flex-col">
        <span class="text-base font-medium text-zinc-900 dark:text-zinc-100">{{ number_format($claps) }} claps</span>
        @if($userClaps > 0)
            <span class="text-sm text-zinc-500 dark:text-zinc-400">You clapped {{ $userClaps }} {{ Str::plural('time', $userClaps) }}</span>
        @endif
    </div>
</div>