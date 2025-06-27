<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\OrganizationPhoneNumber;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class OrganizationPhoneNumberFactory extends Factory
{
    protected $model = OrganizationPhoneNumber::class;

    public function definition(): array
    {
        return [
            'phone_number' => $this->faker->phoneNumber(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'organization_id' => Organization::factory(),
        ];
    }
}
