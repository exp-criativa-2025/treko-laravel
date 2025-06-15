    <?php

    use App\Models\AcademicEntity;
    use App\Models\Campaign;
    use App\Models\User;
    use Illuminate\Foundation\Testing\RefreshDatabase;
    use Laravel\Sanctum\Sanctum;

    uses(RefreshDatabase::class);

    beforeEach(function () {

    });

    it('pode listar campanhas para o usuário autenticado', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $academicEntity = AcademicEntity::factory()->create(['user_id' => $user->id]);

        $campaignsBelongingToUser = Campaign::factory()->count(3)->create([
            'academic_entity_id' => $academicEntity->id,
        ]);

        $anotherUser = User::factory()->create();
        $anotherAcademicEntity = AcademicEntity::factory()->create(['user_id' => $anotherUser->id]);
        $campaignsBelongingToAnotherUser = Campaign::factory()->count(2)->create([
            'academic_entity_id' => $anotherAcademicEntity->id,
        ]);

        $response = $this->getJson('/api/campaigns');

        $response->assertOk()
            ->assertJsonCount(3)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'name',
                    'goal',
                    'start_date',
                    'end_date',
                    'academic_entity_id',
                    'created_at',
                    'updated_at',
                ],
            ]);

        foreach ($campaignsBelongingToUser as $campaign) {
            $response->assertJsonFragment([
                'id' => $campaign->id,
                'name' => $campaign->name,
                'goal' => $campaign->goal,
            ]);
        }

        foreach ($campaignsBelongingToAnotherUser as $campaign) {
            $response->assertJsonMissing([
                'id' => $campaign->id,
            ]);
        }
    });

    it('retorna 401 se um usuário não autenticado tentar listar campanhas', function () {
        $response = $this->getJson('/api/campaigns');

        $response->assertUnauthorized();
    });


    it('pode criar uma nova campanha para a entidade acadêmica do usuário autenticado', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $academicEntity = AcademicEntity::factory()->create(['user_id' => $user->id]);

        $campaignData = [
            'name' => 'Nova Campanha de Teste',
            'goal' => 1000.00, // Alterado para um valor numérico
            'start_date' => '2024-01-01',
            'end_date' => '2024-12-31',
            'academic_entity_id' => $academicEntity->id,
        ];

        $response = $this->postJson('/api/campaigns', $campaignData);

        $response->assertCreated()
            ->assertJsonFragment([
                'name' => 'Nova Campanha de Teste',
                'goal' => 1000.00, 
                'academic_entity_id' => $academicEntity->id,
            ]);

        $this->assertDatabaseHas('campaigns', [
            'name' => 'Nova Campanha de Teste',
            'goal' => 1000.00, // E aqui
            'academic_entity_id' => $academicEntity->id,
        ]);
    });

    it('retorna 422 para dados de campanha inválidos ao armazenar', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $academicEntity = AcademicEntity::factory()->create(['user_id' => $user->id]);

        $response = $this->postJson('/api/campaigns', [
            'goal' => 'Algum objetivo',
            'start_date' => '2024-01-01',
            'end_date' => '2024-12-31',
            'academic_entity_id' => $academicEntity->id,
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);

        $response = $this->postJson('/api/campaigns', [
            'name' => 'Datas Inválidas',
            'goal' => 'Algum objetivo',
            'start_date' => '2024-12-31',
            'end_date' => '2024-01-01',
            'academic_entity_id' => $academicEntity->id,
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['end_date']);

        $response = $this->postJson('/api/campaigns', [
            'name' => 'Entidade Inexistente',
            'goal' => 'Algum objetivo',
            'start_date' => '2024-01-01',
            'end_date' => '2024-12-31',
            'academic_entity_id' => 9999,
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['academic_entity_id']);
    });

    it('retorna 403 se o usuário tentar criar uma campanha para uma entidade acadêmica que não possui', function () {
        $userA = User::factory()->create();
        Sanctum::actingAs($userA, ['*']);

        $userB = User::factory()->create();
        $academicEntityB = AcademicEntity::factory()->create(['user_id' => $userB->id]);

        $campaignData = [
            'name' => 'Campanha Não Autorizada',
            'goal' => 'Testar acesso não autorizado',
            'start_date' => '2024-01-01',
            'end_date' => '2024-12-31',
            'academic_entity_id' => $academicEntityB->id,
        ];

        $response = $this->postJson('/api/campaigns', $campaignData);

        $response->assertForbidden()
            ->assertJson(['message' => 'Acesso não autorizado.']);

        $this->assertDatabaseMissing('campaigns', [
            'name' => 'Campanha Não Autorizada',
        ]);
    });

    it('retorna 401 se um usuário não autenticado tentar armazenar uma campanha', function () {
        $academicEntity = AcademicEntity::factory()->create();
        $campaignData = [
            'name' => 'Campanha Não Autorizada',
            'goal' => 'Teste',
            'start_date' => '2024-01-01',
            'end_date' => '2024-12-31',
            'academic_entity_id' => $academicEntity->id,
        ];

        $response = $this->postJson('/api/campaigns', $campaignData);

        $response->assertUnauthorized();
    });

    it('pode deletar uma campanha que pertence ao usuário autenticado', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $academicEntity = AcademicEntity::factory()->create(['user_id' => $user->id]);

        $campaign = Campaign::factory()->create([
            'academic_entity_id' => $academicEntity->id,
        ]);

        $response = $this->deleteJson('/api/campaigns/' . $campaign->id);

        $response->assertNoContent();

        $this->assertDatabaseMissing('campaigns', ['id' => $campaign->id]);
    });

    it('retorna 403 se o usuário tentar deletar uma campanha que não possui', function () {
        $userA = User::factory()->create();
        Sanctum::actingAs($userA, ['*']);

        $userB = User::factory()->create();
        $academicEntityB = AcademicEntity::factory()->create(['user_id' => $userB->id]);
        $campaignB = Campaign::factory()->create([
            'academic_entity_id' => $academicEntityB->id,
        ]);

        $response = $this->deleteJson('/api/campaigns/' . $campaignB->id);

        $response->assertForbidden()
            ->assertJson(['message' => 'Acesso não autorizado.']);

        $this->assertDatabaseHas('campaigns', ['id' => $campaignB->id]);
    });

    it('retorna 404 se o usuário tentar deletar uma campanha inexistente', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $nonExistentId = 9999;
        $response = $this->deleteJson('/api/campaigns/' . $nonExistentId);

        $response->assertNotFound();
    });

    it('retorna 401 se um usuário não autenticado tentar deletar uma campanha', function () {
        $campaign = Campaign::factory()->create();

        $response = $this->deleteJson('/api/campaigns/' . $campaign->id);

        $response->assertUnauthorized();
    });
    