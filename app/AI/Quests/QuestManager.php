<?php

namespace App\AI\Quests;

use App\Models\Quest;
use App\Models\User;
use Filament\Notifications\Notification;

class QuestManager
{
    /**
     * Complete a quest for a user and award Nex.
     */
    public static function complete(User $user, string $questKey): bool
    {
        $quest = Quest::where('key', $questKey)->first();

        if (! $quest) {
            return false;
        }

        // Check if already completed and not repeatable
        $alreadyCompleted = $user->completedQuests()->where('quest_id', $quest->id)->exists();

        if ($alreadyCompleted && ! $quest->is_repeatable) {
            return false;
        }

        // Award Nex
        $user->increment('nex_balance', $quest->reward_nex);

        // Log completion
        $user->completedQuests()->attach($quest->id, [
            'earned_nex' => $quest->reward_nex,
            'completed_at' => now(),
        ]);

        // Notify if in a context where notifications work (like Livewire)
        if (request()->hasHeader('X-Livewire')) {
            // We use a general event that Livewire can pick up or we can dispatch directly if we had a component instance.
            // Since this is a static manager, we can use the 'dispatch' helper if available, or just rely on the session/event bus.
            // For now, let's assume we want to trigger the Alpine.js listener '@notify.window'.
            // We can do this by broadcasting or using a Livewire dispatch if we are in a LW request.
            if (function_exists('livewire')) {
                \Livewire\Livewire::dispatch('notify', 
                    title: 'Missão Cumprida: ' . $quest->name,
                    message: "Você ganhou {$quest->reward_nex} Nex!",
                    type: 'success'
                );
            }
        }

        return true;
    }
}
