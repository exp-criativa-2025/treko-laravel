<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create a test user for authentication
    $this->actingAs(User::factory()->create());
});

it('can list all users', function () {
    User::factory()->count(3)->create();

    $response = $this->getJson('/api/users');

    $response->assertStatus(200)
             ->assertJsonCount(4); // 3 created + 1 authenticated user
});

it('can show a specific user', function () {
    $user = User::factory()->create();

    $response = $this->getJson("/api/users/{$user->id}");

    $response->assertStatus(200)
             ->assertJson([
                 'id' => $user->id,
                 'name' => $user->name,
                 'email' => $user->email,
             ]);
});

it('returns 404 if user not found when showing', function () {
    $response = $this->getJson('/api/users/999');

    $response->assertStatus(404)
             ->assertJson([
                 'message' => 'Usuário não encontrado',
             ]);
});

it('can update a user', function () {
    $user = User::factory()->create();

    $payload = [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
        'phone' => '11999999999',
        'cpf' => '123.456.789-00',
        'role' => 'admin',
    ];

    $response = $this->putJson("/api/users/{$user->id}", $payload);

    $response->assertStatus(200)
             ->assertJson([
                 'name' => 'Updated Name',
                 'email' => 'updated@example.com',
                 'phone' => '11999999999',
                 'cpf' => '123.456.789-00',
                 'role' => 'admin',
             ]);

    expect($user->fresh()->name)->toBe('Updated Name');
});

it('returns 404 if user not found when updating', function () {
    $payload = [
        'name' => 'Test Name',
        'email' => 'test@example.com',
        'role' => 'user',
    ];

    $response = $this->putJson('/api/users/999', $payload);

    $response->assertStatus(404)
             ->assertJson([
                 'message' => 'Usuário não encontrado',
             ]);
});

it('can delete a user', function () {
    $user = User::factory()->create();

    $response = $this->deleteJson("/api/users/{$user->id}");

    $response->assertStatus(200)
             ->assertJson([
                 'message' => 'Usuário deletado com sucesso',
             ]);

    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});

it('returns 404 if user not found when deleting', function () {
    $response = $this->deleteJson('/api/users/999');

    $response->assertStatus(404)
             ->assertJson([
                 'message' => 'Usuário não encontrado',
             ]);
});
