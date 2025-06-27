<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Organization extends Model
{
    use HasFactory, SoftDeletes, Searchable;

    protected $fillable = [
        'name',
        'building_id',
    ];

    public function phone_numbers()
    {
        return $this->hasMany(OrganizationPhoneNumber::class);
    }

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function activities()
    {
        return $this->belongsToMany(Activity::class);
    }

    /**
     * Настройка индекса для Meilisearch
     */
    public function searchableAs()
    {
        return 'organizations';
    }

    /**
     * Данные для индексации
     */
    public function toSearchableArray()
    {
        $this->loadMissing(['building', 'activities']);

        $array = [
            'id' => $this->id,
            'name' => $this->name,
            'building_id' => $this->building_id,
            'created_at' => $this->created_at?->timestamp,
            'updated_at' => $this->updated_at?->timestamp,
        ];

        // Добавляем данные здания и геолокацию
        if ($this->building) {
            $array['building'] = [
                'id' => $this->building->id,
                'address' => $this->building->address,
                'name' => $this->building->name ?? '',
            ];

            // Геолокация здания
            if ($this->building->lat && $this->building->lng) {
                $array['_geo'] = [
                    'lat' => (float) $this->building->lat,
                    'lng' => (float) $this->building->lng,
                ];
            }
        }

        // Данные о деятельности организации
        if ($this->activities && $this->activities->count() > 0) {
            $array['activities'] = $this->activities->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'name' => $activity->name,
                ];
            })->toArray();

            // Массив ID деятельности для фильтрации
            $array['activity_ids'] = $this->activities->pluck('id')->toArray();
        } else {
            $array['activities'] = [];
            $array['activity_ids'] = [];
        }

        return $array;
    }

    /**
     * Конфигурация для Meilisearch при создании/обновлении индекса
     */
    public function meilisearchSettings(): array
    {
        return [
            'searchableAttributes' => [
                'name'
            ],
            'filterableAttributes' => [
                '_geo',
                'building_id',
                'activity_ids',
                'created_at',
                'updated_at',
            ],
            'sortableAttributes' => [
                'created_at',
                'updated_at',
                'name',
            ],
        ];
    }

    /**
     * Индексировать только не удаленные записи
     */
    public function shouldBeSearchable()
    {
        return !$this->trashed();
    }
}
