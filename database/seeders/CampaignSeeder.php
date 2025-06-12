<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\AcademicEntity;
use Illuminate\Database\Seeder;

class CampaignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $academicEntities = AcademicEntity::all();

        foreach ($academicEntities as $entity) {
            Campaign::create([
                'name' => 'Campaign for ' . $entity->fantasy_name,
                'goal' => fake()->randomFloat(2, 1000, 50000),
                'start_date' => now(),
                'end_date' => now()->addMonths(6),
                'academic_entity_id' => $entity->id,
            ]);
        }
    }
}
