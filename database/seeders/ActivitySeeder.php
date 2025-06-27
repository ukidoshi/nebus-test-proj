<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Activity;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        // Уровень 1
        $level1 = Activity::factory()->count(3)->create();

        // Уровень 2
        $level2 = collect();
        foreach ($level1 as $parent) {
            $level2 = $level2->merge(
                Activity::factory()->count(2)->create([
                    'parent_id' => $parent->id
                ])
            );
        }

        // Уровень 3
        foreach ($level2 as $parent) {
            Activity::factory()->count(2)->create([
                'parent_id' => $parent->id
            ]);
        }
    }
}

