<?php

namespace App\Http\Resources;

use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Organization */
class OrganizationResource extends JsonResource
{
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'building' => [
                'id' => $this->building->id,
                'address' => $this->building->address,
                'lat' => $this->building->lat,
                'lng' => $this->building->lng
            ],
            'phone_numbers' => $this->phone_numbers->pluck('phone_number'),
            'activities' => $this->activities,
        ];
    }
}
