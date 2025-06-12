<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class; // Ensure the model is correctly linked

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => bcrypt('password'), // default password
            'role' => $this->faker->randomElement([
                UserRole::ADMIN,
                UserRole::REPRESENTATIVE,
                UserRole::DONOR,
            ]),
            'cpf' => $this->faker->unique()->numerify('###.###.###-##'),
            'phone' => $this->faker->numerify('(##) #####-####'),
            'remember_token' => Str::random(10),
        ];
    }

    
}
