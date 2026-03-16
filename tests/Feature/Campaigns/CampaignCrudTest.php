<?php

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

$user = null;
$campaign = null;

beforeEach(function () use (&$user, &$campaign) {
    $user = User::factory()->create();
    $campaign = Campaign::factory()->create(['user_id' => $user->id]);
});

it('renders the campaign index page', function () use (&$user, &$campaign) {
    /** @var \Tests\TestCase $this */
    $this->actingAs($user)
        ->get('/campaigns')
        ->assertOk()
        ->assertSee($campaign->title);
});

it('can delete a campaign from the index', function () use (&$user, &$campaign) {
    Livewire::actingAs($user)
        ->test('pages::campaigns.index')
        ->call('delete', $campaign->id)
        ->assertNotified();

    /** @var \Tests\TestCase $this */
    $this->assertDatabaseMissing('campaigns', [
        'id' => $campaign->id,
    ]);
});

it('prevents deleting a campaign you do not own', function () use (&$campaign) {
    $otherUser = User::factory()->create();

    expect(function () use ($otherUser, $campaign) {
        Livewire::actingAs($otherUser)
            ->test('pages::campaigns.index')
            ->call('delete', $campaign->id);
    })->toThrow(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
});

it('renders the edit page for a campaign', function () use (&$user, &$campaign) {
    /** @var \Tests\TestCase $this */
    $this->actingAs($user)
        ->get("/campaigns/{$campaign->id}/edit")
        ->assertOk()
        ->assertSee($campaign->title);
});

it('prevents accessing edit page for unowned campaign', function () use (&$campaign) {
    $otherUser = User::factory()->create();

    /** @var \Tests\TestCase $this */
    $this->actingAs($otherUser)
        ->get("/campaigns/{$campaign->id}/edit")
        ->assertForbidden();
});

it('can update a campaign', function () use (&$user, &$campaign) {
    Livewire::actingAs($user)
        ->test('pages::campaigns.edit', ['campaign' => $campaign])
        ->set('title', 'Updated Title')
        ->set('description', 'Updated description')
        ->call('save')
        ->assertNotified()
        ->assertRedirect('/campaigns');

    /** @var \Tests\TestCase $this */
    $this->assertDatabaseHas('campaigns', [
        'id' => $campaign->id,
        'title' => 'Updated Title',
        'description' => 'Updated description',
    ]);
});

it('renders the show page for a campaign', function () use (&$user, &$campaign) {
    /** @var \Tests\TestCase $this */
    $this->actingAs($user)
        ->get("/campaigns/{$campaign->id}/show")
        ->assertOk()
        ->assertSee($campaign->title)
        ->assertSee($campaign->starting_level);
});

it('prevents accessing show page for unowned campaign', function () use (&$campaign) {
    $otherUser = User::factory()->create();

    /** @var \Tests\TestCase $this */
    $this->actingAs($otherUser)
        ->get("/campaigns/{$campaign->id}/show")
        ->assertForbidden();
});
