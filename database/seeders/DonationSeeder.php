<?php

namespace Database\Seeders;

use App\Models\Donation;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Database\Seeder;

class DonationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $campaigns = Campaign::all();

        foreach ($campaigns as $campaign) {
            for ($i = 0; $i < 50; $i++) {
                $user = $users->random();
                $date = fake()->dateTimeBetween('2025-01-01', '2025-12-31');
                Donation::create([
                    'donated' => fake()->randomFloat(2, 10, 1000),
                    'created_at' => $date,
                    'updated_at' => $date,
                    'user_id' => $user->id,
                    'campaign_id' => $campaign->id,
                ]);
            }
        }
    }
}
