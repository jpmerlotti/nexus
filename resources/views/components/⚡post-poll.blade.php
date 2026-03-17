<?php

use App\Models\PollVote;
use Livewire\Volt\Component;
use Illuminate\Support\Str;

new class extends Component
{
    public string $uuid;
    public string $question;
    public array $options;
    
    public bool $hasVoted = false;
    public ?int $selectedOption = null;
    public array $results = [];
    public int $totalVotes = 0;

    public function mount(string $uuid, string $question, array $options)
    {
        $this->uuid = $uuid;
        $this->question = $question;
        $this->options = $options;
        
        $this->hasVoted = session()->has('poll_' . $this->uuid);
        $this->selectedOption = session()->get('poll_' . $this->uuid);
        
        $this->loadResults();
    }

    public function vote(int $index)
    {
        if ($this->hasVoted) {
            return;
        }

        $vote = PollVote::firstOrCreate(
            ['poll_id' => $this->uuid, 'option_index' => $index],
            ['count' => 0]
        );
        $vote->increment('count');

        $this->hasVoted = true;
        $this->selectedOption = $index;
        session()->put('poll_' . $this->uuid, $index);

        $this->loadResults();
    }

    public function loadResults()
    {
        $votes = PollVote::where('poll_id', $this->uuid)->pluck('count', 'option_index')->toArray();
        $this->totalVotes = array_sum($votes);
        
        foreach ($this->options as $index => $option) {
            $count = $votes[$index] ?? 0;
            $percentage = $this->totalVotes > 0 ? round(($count / $this->totalVotes) * 100) : 0;
            $this->results[$index] = [
                'count' => $count,
                'percentage' => $percentage,
            ];
        }
    }
};
?>

<div>
    <h3 class="text-xl font-bold mb-4 text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
        <flux:icon icon="chart-bar" variant="outline" class="h-5 w-5 text-indigo-500" />
        {{ $question }}
    </h3>

    <div class="space-y-3">
        @foreach($options as $index => $option)
            @php
                $result = $results[$index] ?? ['count' => 0, 'percentage' => 0];
                $isSelected = $hasVoted && $selectedOption === $index;
            @endphp
            
            @if(!$hasVoted)
                <button 
                    wire:click="vote({{ $index }})"
                    class="w-full text-left px-4 py-3 rounded-lg border border-zinc-200 dark:border-zinc-700 hover:border-indigo-400 hover:bg-indigo-50 dark:hover:border-indigo-500/50 dark:hover:bg-indigo-500/10 transition-colors group flex items-center justify-between shadow-sm bg-white dark:bg-zinc-800"
                >
                    <span class="text-zinc-700 dark:text-zinc-300 font-medium group-hover:text-indigo-700 dark:group-hover:text-indigo-300">{{ $option['text'] }}</span>
                    <div class="h-5 w-5 rounded-full border-2 border-zinc-300 dark:border-zinc-600 group-hover:border-indigo-500 transition-colors"></div>
                </button>
            @else
                <div class="relative w-full rounded-lg overflow-hidden border {{ $isSelected ? 'border-indigo-300 dark:border-indigo-500/50 bg-indigo-50/50 dark:bg-indigo-500/10' : 'border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800' }}">
                    {{-- Progress bar background --}}
                    <div 
                        class="absolute top-0 left-0 bottom-0 transition-all duration-1000 ease-out {{ $isSelected ? 'bg-indigo-100 dark:bg-indigo-500/20' : 'bg-zinc-100 dark:bg-zinc-700' }}"
                        style="width: {{ $result['percentage'] }}%"
                    ></div>
                    
                    {{-- Content --}}
                    <div class="relative z-10 flex items-center justify-between px-4 py-3">
                        <div class="flex items-center gap-3">
                            @if($isSelected)
                                <flux:icon icon="check-circle" variant="solid" class="h-5 w-5 text-indigo-500" />
                            @endif
                            <span class="font-medium {{ $isSelected ? 'text-indigo-800 dark:text-indigo-300' : 'text-zinc-700 dark:text-zinc-300' }}">
                                {{ $option['text'] }}
                            </span>
                        </div>
                        <div class="font-bold text-sm {{ $isSelected ? 'text-indigo-700 dark:text-indigo-400' : 'text-zinc-500 dark:text-zinc-400' }}">
                            {{ $result['percentage'] }}%
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    @if($hasVoted)
        <div class="mt-4 text-xs font-medium text-zinc-500 dark:text-zinc-400 text-right">
            {{ number_format($totalVotes) }} {{ Str::plural('vote', $totalVotes) }} recorded
        </div>
    @endif
</div>