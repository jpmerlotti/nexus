<?php

use App\Models\Post;
use App\Models\Comment;
use Livewire\Volt\Component;

new class extends Component
{
    public Post $post;
    public string $author = '';
    public string $content = '';

    public function mount(Post $post)
    {
        $this->post = $post;
    }

    public function addComment()
    {
        $this->validate([
            'author' => 'required|string|max:255',
            'content' => 'required|string|max:1000',
        ]);

        $this->post->comments()->create([
            'author' => $this->author,
            'content' => $this->content,
            'score' => 0,
        ]);

        $this->reset(['author', 'content']);
        
        session()->flash('message', 'Comentário enviado com sucesso!');
    }

    public function getCommentsProperty()
    {
        return $this->post->comments()->latest()->get();
    }
};
?>

<div class="mt-12 w-full max-w-3xl mx-auto border-t border-zinc-200 dark:border-zinc-800 pt-8">
    <h3 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mb-6 font-display">
        Comentários ({{ $this->comments->count() }})
    </h3>

    {{-- Form --}}
    <form wire:submit="addComment" class="mb-10 bg-zinc-50 dark:bg-zinc-900/50 p-6 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm">
        <div class="space-y-4">
            <flux:input wire:model="author" label="Seu Nome" placeholder="Como você quer ser chamado?" required />
            <flux:textarea wire:model="content" label="Comentário" placeholder="O que você achou disso?" rows="3" required />
            
            <div class="flex items-center justify-between pt-2">
                <div>
                    @if (session()->has('message'))
                        <span class="text-sm font-medium text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                            <flux:icon icon="check-circle" variant="solid" class="h-4 w-4" />
                            {{ session('message') }}
                        </span>
                    @endif
                </div>
                <flux:button type="submit" variant="primary">
                    Enviar Comentário
                </flux:button>
            </div>
        </div>
    </form>

    {{-- List --}}
    <div class="space-y-6">
        @forelse ($this->comments as $comment)
            <div class="flex gap-4 group">
                <div class="h-10 w-10 shrink-0 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-indigo-700 dark:text-indigo-300 font-bold text-sm">
                    {{ Str::upper(substr($comment->author, 0, 1)) }}
                </div>
                
                <div class="flex-1 space-y-1">
                    <div class="flex items-center justify-between">
                        <span class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $comment->author }}</span>
                        <span class="text-xs text-zinc-500" title="{{ $comment->created_at }}">
                            {{ $comment->created_at->diffForHumans() }}
                        </span>
                    </div>
                    
                    <div class="text-zinc-700 dark:text-zinc-300 text-sm leading-relaxed whitespace-pre-line">
                        {{ $comment->content }}
                    </div>
                    
                    {{-- Future: Reply / Upvote actions could go here --}}
                </div>
            </div>
        @empty
            <div class="text-center py-10">
                <div class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-400 mb-4">
                    <flux:icon icon="chat-bubble-bottom-center-text" variant="outline" class="h-8 w-8" />
                </div>
                <h4 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">Nenhum comentário ainda</h4>
                <p class="text-sm text-zinc-500 mt-1">Seja o primeiro a compartilhar sua opinião.</p>
            </div>
        @endforelse
    </div>
</div>