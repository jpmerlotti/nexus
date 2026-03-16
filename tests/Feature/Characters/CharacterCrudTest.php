<?php

use App\Models\Character;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

$user = null;
$character = null;

beforeEach(function () use (&$user, &$character) {
    $user = User::factory()->create();
    $character = Character::factory()->create(['user_id' => $user->id]);
});

it('renders the character index page', function () use (&$user, &$character) {
    /** @var \Tests\TestCase $this */
    $this->actingAs($user)
        ->get('/characters')
        ->assertOk()
        ->assertSee($character->name);
});

it('can delete a character from the index', function () use (&$user, &$character) {
    Livewire::actingAs($user)
        ->test('pages::characters.index')
        ->call('delete', $character->id)
        ->assertNotified();

    /** @var \Tests\TestCase $this */
    $this->assertDatabaseMissing('characters', [
        'id' => $character->id,
    ]);
});

it('prevents deleting a character you do not own', function () use (&$character) {
    $otherUser = User::factory()->create();

    expect(function () use ($otherUser, $character) {
        Livewire::actingAs($otherUser)
            ->test('pages::characters.index')
            ->call('delete', $character->id);
    })->toThrow(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
});

it('renders the edit page for a character', function () use (&$user, &$character) {
    /** @var \Tests\TestCase $this */
    $this->actingAs($user)
        ->get("/characters/{$character->id}/edit")
        ->assertOk()
        ->assertSee($character->name);
});

it('prevents accessing edit page for unowned character', function () use (&$character) {
    $otherUser = User::factory()->create();

    /** @var \Tests\TestCase $this */
    $this->actingAs($otherUser)
        ->get("/characters/{$character->id}/edit")
        ->assertForbidden();
});

it('can update a character', function () use (&$user, &$character) {
    Livewire::actingAs($user)
        ->test('pages::characters.edit', ['character' => $character])
        ->set('name', 'Updated Name')
        ->set('race', 'Elf')
        ->call('save')
        ->assertNotified()
        ->assertRedirect('/characters');

    /** @var \Tests\TestCase $this */
    $this->assertDatabaseHas('characters', [
        'id' => $character->id,
        'name' => 'Updated Name',
        'race' => 'Elf',
    ]);
});

it('renders the show page for a character', function () use (&$user, &$character) {
    /** @var \Tests\TestCase $this */
    $this->actingAs($user)
        ->get("/characters/{$character->id}/show")
        ->assertOk()
        ->assertSee($character->name)
        ->assertSee($character->race);
});

it('prevents accessing show page for unowned character', function () use (&$character) {
    $otherUser = User::factory()->create();

    /** @var \Tests\TestCase $this */
    $this->actingAs($otherUser)
        ->get("/characters/{$character->id}/show")
        ->assertForbidden();
});
