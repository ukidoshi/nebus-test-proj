<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\Building;
use App\Models\Organization;
use Closure;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class OrganizationFactory extends Factory
{
    protected $model = Organization::class;

    public function definition()
    {
        return [
            'name' => $this->faker->company(),
            'building_id' => Building::factory(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
