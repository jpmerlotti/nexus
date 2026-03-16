<?php

use App\Models\Campaign;
use App\Models\Character;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('redirects to character linking after campaign creation', function () {
    $user = User::factory()->create();
    actingAs($user);

    Livewire::test('pages::campaigns.create')
        ->set('title', 'New Adventure')
        ->set('description', 'A grand quest')
        ->call('save')
        ->assertRedirectContains('/link-character');

    $campaign = Campaign::where('title', 'New Adventure')->first();
    expect($campaign)->not->toBeNull();
});

it('lists only available characters on the linking page', function () {
    $user = User::factory()->create();
    actingAs($user);

    $campaign = Campaign::factory()->create(['user_id' => $user->id]);

    // Character already in a campaign
    $bookedChar = Character::factory()->create(['user_id' => $user->id]);
    $campaign->characters()->attach($bookedChar);

    // Free character
    $freeChar = Character::factory()->create(['user_id' => $user->id, 'name' => 'Free Hero']);

    Livewire::test('pages::campaigns.link-character', ['campaign' => $campaign])
        ->assertSee('Free Hero')
        ->assertDontSee($bookedChar->name);
});

it('links a character and redirects to campaign show', function () {
    $user = User::factory()->create();
    actingAs($user);

    $campaign = Campaign::factory()->create(['user_id' => $user->id]);
    $character = Character::factory()->create(['user_id' => $user->id]);

    Livewire::test('pages::campaigns.link-character', ['campaign' => $campaign])
        ->call('selectCharacter', $character->id)
        ->assertRedirect(route('campaigns.show', $campaign));

    expect($campaign->characters()->pluck('characters.id')->toArray())->toContain($character->id);
});
