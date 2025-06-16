<?php

use App\Models\User;
use App\Models\Campaign;
use App\Models\AcademicEntity;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create an authenticated user
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('can list all campaigns', function () {
    $entity = AcademicEntity::factory()->create(['user_id' => $this->user->id]);
    Campaign::factory()->count(3)->create(['academic_entity_id' => $entity->id]);

    $response = $this->getJson('/api/campaigns');

    $response->assertStatus(200)
             ->assertJsonCount(3);
});

it('can show a specific campaign', function () {
    $entity = AcademicEntity::factory()->create(['user_id' => $this->user->id]);
    $campaign = Campaign::factory()->create(['academic_entity_id' => $entity->id]);

    $response = $this->getJson("/api/campaigns/{$campaign->id}");

    $response->assertStatus(200)
             ->assertJson([
                 'id' => $campaign->id,
                 'name' => $campaign->name,
             ]);
});

it('returns 404 when showing a non-existent campaign', function () {
    $response = $this->getJson('/api/campaigns/999');

    $response->assertStatus(404);
});

it('can create a campaign for owned academic entity', function () {
    $entity = AcademicEntity::factory()->create(['user_id' => $this->user->id]);

    $payload = [
        'name' => 'Nova Campanha',
        'goal' => 1000,
        'start_date' => now()->toDateString(),
        'end_date' => now()->addDays(30)->toDateString(),
        'academic_entity_id' => $entity->id,
    ];

    $response = $this->postJson('/api/campaigns', $payload);

    $response->assertStatus(201)
             ->assertJson([
                 'name' => 'Nova Campanha',
                 'academic_entity_id' => $entity->id,
             ]);

    $this->assertDatabaseHas('campaigns', ['name' => 'Nova Campanha']);
});

it('forbids creating a campaign for an entity not owned by the user', function () {
    $otherUser = User::factory()->create();
    $entity = AcademicEntity::factory()->create(['user_id' => $otherUser->id]);

    $payload = [
        'name' => 'Campanha Inválida',
        'goal' => 500,
        'start_date' => now()->toDateString(),
        'end_date' => now()->addDays(30)->toDateString(),
        'academic_entity_id' => $entity->id,
    ];

    $response = $this->postJson('/api/campaigns', $payload);

    $response->assertStatus(403)
             ->assertJson([
                 'message' => 'Acesso não autorizado.',
             ]);
});

it('can delete a campaign owned by the user', function () {
    $entity = AcademicEntity::factory()->create(['user_id' => $this->user->id]);
    $campaign = Campaign::factory()->create(['academic_entity_id' => $entity->id]);

    $response = $this->deleteJson("/api/campaigns/{$campaign->id}");

    $response->assertStatus(204);
    $this->assertDatabaseMissing('campaigns', ['id' => $campaign->id]);
});

it('forbids deleting a campaign not owned by the user', function () {
    $otherUser = User::factory()->create();
    $entity = AcademicEntity::factory()->create(['user_id' => $otherUser->id]);
    $campaign = Campaign::factory()->create(['academic_entity_id' => $entity->id]);

    $response = $this->deleteJson("/api/campaigns/{$campaign->id}");

    $response->assertStatus(403)
             ->assertJson([
                 'message' => 'Acesso não autorizado.',
             ]);
});
