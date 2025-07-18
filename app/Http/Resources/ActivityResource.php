<?php

namespace App\Http\Resources;

use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Activity */
class ActivityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'children' => ActivityResource::collection($this->whenLoaded('children')),
            'parent_id' => $this->parent_id,
            'parent' => new ActivityResource($this->whenLoaded('parent')),
        ];
    }
}
