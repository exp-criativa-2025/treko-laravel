<?php

namespace Database\Factories;

use App\Models\AcademicEntity;
use Illuminate\Database\Eloquent\Factories\Factory;

class AcademicEntityFactory extends Factory
{
    protected $model = AcademicEntity::class;

    public function definition()
    {
        return [
            'type' => 'university',
            'fantasy_name' => $this->faker->company,
            'cnpj' => $this->faker->numerify('##.###.###/####-##'),
            'foundation_date' => $this->faker->date,
            'status' => 'active',
            'cep' => $this->faker->postcode,
            'user_id' => \App\Models\User::factory(),
        ];
    }
}