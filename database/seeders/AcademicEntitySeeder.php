<?php

namespace Database\Seeders;

use App\Enums\AcademicEntityType;
use App\Models\AcademicEntity;
use App\Models\User;
use Illuminate\Database\Seeder;

class AcademicEntitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::factory()->count(10)->create();

        foreach ($users as $user) {
            AcademicEntity::create([
                'type' => fake()->randomElement([
                    AcademicEntityType::ACADEMIC_CENTER,
                    AcademicEntityType::CENTRAL_DIRECTORY,
                    AcademicEntityType::ATHLETIC_ASSOCIATION,
                    AcademicEntityType::CLUB,
                    AcademicEntityType::BATTERY,
                ]),
                'fantasy_name' => 'Entity ' . $user->id,
                'cnpj' => $this->generateFakeCnpj(),
                'foundation_date' => fake()->date(),
                'status' => 'active',
                'cep' => fake()->postcode(),
                'user_id' => $user->id,
            ]);
        }
    }

    function generateFakeCnpj()
    {
        // 8 números aleatórios
        $numbers = '';
        for ($i = 0; $i < 8; $i++) {
            $numbers .= rand(0, 9);
        }

        // Os 4 últimos dígitos antes dos dígitos verificadores são fixos: "0001"
        $branch = '0001';

        // Dois dígitos verificadores fake aleatórios (não validados)
        $dv1 = rand(0, 9);
        $dv2 = rand(0, 9);

        return $numbers . $branch . $dv1 . $dv2;
    }
}
