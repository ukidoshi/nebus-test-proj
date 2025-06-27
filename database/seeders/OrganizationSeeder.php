<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Building;
use App\Models\Organization;
use App\Models\OrganizationPhoneNumber;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Organization::factory()
            ->count(15)
            ->recycle(Building::factory()->count(5)->create())
            ->create()
            ->each(function ($org) {
                // Добавляем от 1 до 3 телефонов
                OrganizationPhoneNumber::factory()->count(rand(1, 3))->create([
                    'organization_id' => $org->id,
                ]);

                // Привязываем 1-3 случайные деятельности
                $activityIds = Activity::inRandomOrder()->take(rand(1, 3))->pluck('id');
                $org->activities()->attach($activityIds);
            });
    }
}
