<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Building;
use App\Models\Organization;
use App\Models\OrganizationPhoneNumber;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            BuildingSeeder::class,
            ActivitySeeder::class,
            OrganizationSeeder::class,
        ]);

        // 1. Создаем активности
//        Activity::factory(20)->create();

        // 2. Создаем здания
//        $buildings = Building::factory(5)->create();

        // 3. Создаем организации и привязываем к ним телефоны
//        $organizations = Organization::factory(20)
//            ->recycle($buildings)
//            ->has(OrganizationPhoneNumber::factory()->count(3), 'phone_numbers')
//            ->create();

        // 4. Привязываем случайные активности к каждой организации
//        $organizations->each(function (Organization $org) use ($activities) {
//            $org->activities()->attach(
//                $activities->random(rand(1, 3))->pluck('id')->toArray()
//            );
//        });

//        $activities = Activity::factory(20)->create();
//
//        // Затем создаем 20 организаций
//        $organizations = Organization::factory(20)
//            ->has(OrganizationPhoneNumber::factory()->count(3), 'phone_numbers')
//            ->recycle(Building::factory()->count(5)->create())
//            ->create();
//
//        // Теперь привязываем случайные активности к каждой организации
//        $organizations->each(function (Organization $organization) use ($activities) {
//            $organization->activities()->attach(
//                $activities->random(rand(1, 3))->pluck('id')->toArray()
//            );
//        });
    }
}
