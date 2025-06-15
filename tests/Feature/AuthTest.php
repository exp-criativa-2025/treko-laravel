<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\DB;

use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

it('registers a new user', function () {
    $response = postJson('/api/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'cpf' => '123.456.789-00',
        'phone' => '41988022368',
        'password' => 'password123',
    ]);

    $response->assertSuccessful()
        ->assertJsonStructure([
            'data' => ['id', 'name', 'email', 'token'],
            'message',
        ]);
});

it('logs in a user', function () {
    $user = User::factory()->create([
        'password' => bcrypt('password123'),
    ]);

    $response = postJson('/api/login', [
        'email' => $user->email,
        'password' => 'password123',
    ]);

    $response->assertOk()
        ->assertJsonStructure([
            'data' => ['id', 'name', 'email', 'token'],
            'message',
        ]);
});

it('logs out a user', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $token = $user->createToken('test')->plainTextToken;

    $response = postJson('/api/logout');

    $response->assertOk()
        ->assertJson([
            'message' => 'Você fez logout',
        ]);
});

it('changes password successfully', function () {
    $user = User::factory()->create([
        'password' => bcrypt('old-password'),
    ]);

    Sanctum::actingAs($user);

    $response = postJson('/api/change-password', [
        'current_password' => 'old-password',
        'new_password' => 'new-password',
        'new_password_confirmation' => 'new-password',
    ]);

    $response->assertOk()
        ->assertJson([
            'message' => 'Senha alterada com sucesso',
        ]);
});
