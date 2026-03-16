<?php

use App\Models\Campaign;
use App\Models\Character;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->campaign = Campaign::factory()->create(['user_id' => $this->user->id]);
});

it('can link a character to a campaign in the show page', function () {
    $character = Character::factory()->create(['user_id' => $this->user->id]);

    Livewire::actingAs($this->user)
        ->test('pages::campaigns.show', ['campaign' => $this->campaign])
        ->set('characterToAdd', $character->id)
        ->call('addCharacter')
        ->assertNotified();

    expect($this->campaign->characters()->count())->toBe(1);
});

it('auto-selects the character if only one is linked to the campaign', function () {
    $character = Character::factory()->create(['user_id' => $this->user->id]);
    $this->campaign->characters()->attach($character);

    Livewire::actingAs($this->user)
        ->test('pages::campaigns.play-campaign', ['campaign' => $this->campaign])
        ->assertSet('characterId', $character->id);
});

it('does not auto-select if multiple characters are linked', function () {
    $chars = Character::factory()->count(2)->create(['user_id' => $this->user->id]);
    $this->campaign->characters()->attach($chars);

    Livewire::actingAs($this->user)
        ->test('pages::campaigns.play-campaign', ['campaign' => $this->campaign])
        ->assertSet('characterId', null);
});

it('can manually select a character in the play view', function () {
    $chars = Character::factory()->count(2)->create(['user_id' => $this->user->id]);
    $this->campaign->characters()->attach($chars);

    Livewire::actingAs($this->user)
        ->test('pages::campaigns.play-campaign', ['campaign' => $this->campaign])
        ->call('selectCharacter', $chars[1]->id)
        ->assertSet('characterId', $chars[1]->id)
        ->assertNotified();
});
