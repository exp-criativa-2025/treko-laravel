<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'phone' => fake()->phoneNumber(),
            'cpf' => $this->generateValidCpf(),
            'role' => UserRole::ADMIN,
        ]);
    }

    function generateValidCpf(): string
    {
        $cpf = [];

        // Generate first 9 digits
        for ($i = 0; $i < 9; $i++) {
            $cpf[] = rand(0, 9);
        }

        // Calculate first digit
        $sum = 0;
        for ($i = 0, $j = 10; $i < 9; $i++, $j--) {
            $sum += $cpf[$i] * $j;
        }
        $remainder = $sum % 11;
        $cpf[] = ($remainder < 2) ? 0 : 11 - $remainder;

        // Calculate second digit
        $sum = 0;
        for ($i = 0, $j = 11; $i < 10; $i++, $j--) {
            $sum += $cpf[$i] * $j;
        }
        $remainder = $sum % 11;
        $cpf[] = ($remainder < 2) ? 0 : 11 - $remainder;

        // Format as XXX.XXX.XXX-XX
        return sprintf('%d%d%d.%d%d%d.%d%d%d-%d%d', ...$cpf);
    }
}
