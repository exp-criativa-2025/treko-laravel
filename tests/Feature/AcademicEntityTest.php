<?php

use App\Models\AcademicEntity;
use App\Models\User;

test('list all academic entities', function () {
    $user = User::factory()->create();
    AcademicEntity::factory()->count(3)->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->getJson('/api/academic-entities');

    $response->assertStatus(200)
            ->assertJsonCount(3);
});

test('create new academic entity', function () {
    $user = User::factory()->create();
    
    $data = [
        'type' => 'university',
        'fantasy_name' => 'Universidade Teste',
        'cnpj' => '12.345.678/0001-90',
        'foundation_date' => '2020-01-01',
        'status' => 'active',
        'cep' => '12345-678',
        'user_id' => $user->id
    ];

    $response = $this->actingAs($user)
                    ->postJson('/api/academic-entities', $data);

    $response->assertStatus(201)
        ->assertJson($data);
});

test('show specific academic entity', function () {
    $user = User::factory()->create();
    $entity = AcademicEntity::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)
                    ->getJson("/api/academic-entities/{$entity->id}");

    $response->assertStatus(200)
            ->assertJson(['id' => $entity->id]);
});

test('update academic entity', function () {
    $user = User::factory()->create();
    $entity = AcademicEntity::factory()->create(['user_id' => $user->id]);

    $newData = ['fantasy_name' => 'Nome Atualizado'];

    $response = $this->actingAs($user)
                    ->putJson("/api/academic-entities/{$entity->id}", $newData);

    $response->assertStatus(200)
            ->assertJson($newData);
});

test('delete academic entity', function () {
    $user = User::factory()->create();
    $entity = AcademicEntity::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)
                    ->deleteJson("/api/academic-entities/{$entity->id}");

    $response->assertStatus(204);
    $this->assertDatabaseMissing('academic_entities', ['id' => $entity->id]);
});