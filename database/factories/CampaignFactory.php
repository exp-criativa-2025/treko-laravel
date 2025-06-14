<?php

namespace Database\Factories;

use App\Models\AcademicEntity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Campaign>
 */
class CampaignFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
        'name' => $this->faker->sentence(3),
        'goal' => $this->faker->randomFloat(2, 1000, 10000),
        'start_date' => now(),
        'end_date' => now()->addYear(),
        'academic_entity_id' => AcademicEntity::factory()
        ];
    }
}
