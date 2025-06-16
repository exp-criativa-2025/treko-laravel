<?php

namespace Database\Factories;

use App\Models\Donation;
use Illuminate\Database\Eloquent\Factories\Factory;

class DonationFactory extends Factory
{
    protected $model = Donation::class;

    public function definition()
    {
        return [
            'donated' => $this->faker->randomFloat(2, 1, 1000),
            'user_id' => \App\Models\User::factory(),
            'campaign_id' => \App\Models\Campaign::factory(),
            'created_at' => $this->faker->dateTimeBetween('2025-01-01', '2025-12-31'),
            'updated_at' => $this->faker->dateTimeBetween('2025-01-01', '2025-12-31'),
        ];
    }
}