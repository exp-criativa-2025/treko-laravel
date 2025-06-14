<?php

namespace Tests\Feature;

use App\Models\Donation;
use App\Models\User;
use App\Models\Campaign; 
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DonationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_donation()
{
    // 1. Cria o usuário
    $user = User::factory()->create();
    
    // 2. Cria a entidade acadêmica (com todos os campos obrigatórios)
    $academicEntity = AcademicEntity::factory()->create([
        'user_id' => $user->id // Associa ao usuário criado
    ]);
    
    // 3. Cria a campanha vinculada à entidade acadêmica
    $campaign = Campaign::factory()->create([
        'academic_entity_id' => $academicEntity->id,
        'name' => 'Campanha de Livros',
        'goal' => 5000,
        'start_date' => now(),
        'end_date' => now()->addMonth()
    ]);

    // 4. Faz a doação
    $response = $this->actingAs($user)->postJson('/api/donations', [
        'donated' => 100.50,
        'date' => now()->toDateTimeString(),
        'campaign_id' => $campaign->id
    ]);

    // 5. Verificações
    $response->assertStatus(201)
            ->assertJson([
                'donated' => 100.50,
                'user_id' => $user->id,
                'campaign_id' => $campaign->id
            ]);
    
    // Verifica se a doação foi realmente criada no banco
    $this->assertDatabaseHas('donations', [
        'donated' => 100.50,
        'user_id' => $user->id
    ]);
}

    // Adicione outros testes para listar, mostrar, atualizar e deletar
}