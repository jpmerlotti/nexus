@props(['blocks' => []])

@if(!empty($blocks))
    <div class="space-y-8">
        @foreach($blocks as $block)
            @php $data = $block['data']; @endphp

            @switch($block['type'])
                @case('header')
                    <flux:heading :level="$data['level']" size="xl" class="font-bold text-zinc-900 dark:text-zinc-100">
                        {{ $data['value'] }}
                    </flux:heading>
                    @break

                @case('text')
                    <div class="prose dark:prose-invert max-w-none text-zinc-700 dark:text-zinc-300">
                        {!! str($data['value'])->sanitizeHtml() !!}
                    </div>
                    @break

                @case('image')
                    <figure class="my-6">
                        <img src="{{ Storage::url($data['src']) }}" alt="{{ $data['caption'] ?? 'Image' }}" class="rounded-xl shadow-md w-full object-cover">
                        @if(!empty($data['caption']))
                            <figcaption class="mt-3 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                {{ $data['caption'] }}
                            </figcaption>
                        @endif
                    </figure>
                    @break

                @case('poll')
                    <div class="my-8 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900 p-6 shadow-sm">
                        <livewire:post-poll :uuid="$data['uuid']" :question="$data['question']" :options="$data['options']" :wire:key="'poll-'.$data['uuid']" />
                    </div>
                    @break

                @case('callout')
                    @php
                        $calloutColors = [
                            'info' => 'bg-blue-50/50 dark:bg-blue-500/10 border-blue-200 dark:border-blue-500/20 text-blue-800 dark:text-blue-300',
                            'warning' => 'bg-amber-50/50 dark:bg-amber-500/10 border-amber-200 dark:border-amber-500/20 text-amber-800 dark:text-amber-300',
                            'tip' => 'bg-emerald-50/50 dark:bg-emerald-500/10 border-emerald-200 dark:border-emerald-500/20 text-emerald-800 dark:text-emerald-300',
                            'danger' => 'bg-red-50/50 dark:bg-red-500/10 border-red-200 dark:border-red-500/20 text-red-800 dark:text-red-300',
                        ];
                        $calloutIcons = [
                            'info' => 'information-circle',
                            'warning' => 'exclamation-triangle',
                            'tip' => 'light-bulb',
                            'danger' => 'x-circle',
                        ];
                        $style = $data['style'] ?? 'info';
                    @endphp
                    <div class="my-6 flex gap-4 rounded-xl border p-4 {{ $calloutColors[$style] }}">
                        <flux:icon :icon="$calloutIcons[$style]" variant="outline" class="h-6 w-6 shrink-0 mt-0.5" />
                        <div class="text-sm leading-relaxed">
                            {{ $data['content'] }}
                        </div>
                    </div>
                    @break

                @case('code')
                    <div x-data="{ copied: false }" class="my-6 overflow-hidden rounded-xl bg-zinc-950 dark:bg-black border border-zinc-800 shadow-sm">
                        <div class="flex items-center justify-between bg-zinc-900 px-4 py-2 border-b border-zinc-800">
                            <span class="text-xs font-medium text-zinc-400 uppercase tracking-wider">{{ $data['language'] ?? 'code' }}</span>
                            <button 
                                @click="navigator.clipboard.writeText($refs.code.innerText); copied = true; setTimeout(() => copied = false, 2000)"
                                class="text-zinc-500 hover:text-zinc-300 transition-colors"
                                title="Copy code"
                            >
                                <flux:icon icon="clipboard-document" variant="outline" class="h-4 w-4" x-show="!copied" />
                                <flux:icon icon="check" variant="outline" class="h-4 w-4 text-emerald-500" x-show="copied" x-cloak />
                            </button>
                        </div>
                        <div class="p-4 overflow-x-auto">
                            <pre><code x-ref="code" class="text-sm text-zinc-300 font-mono">{{ $data['code'] }}</code></pre>
                        </div>
                    </div>
                    @break

                @case('quote')
                    <blockquote class="my-8 border-l-4 border-zinc-300 dark:border-zinc-700 pl-6 italic text-zinc-600 dark:text-zinc-400">
                        <p class="text-lg leading-relaxed mb-2">"{{ $data['text'] }}"</p>
                        @if(!empty($data['citation']))
                            <footer class="text-sm font-medium text-zinc-500 dark:text-zinc-500">
                                &mdash; {{ $data['citation'] }}
                            </footer>
                        @endif
                    </blockquote>
                    @break

                @case('divider')
                    <div class="py-8">
                        <flux:separator />
                    </div>
                    @break

            @endswitch
        @endforeach
    </div>
@endif