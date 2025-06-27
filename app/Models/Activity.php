<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'parent_id'
    ];

    /**
     * Максимальный уровень вложенности (3 уровня)
     */
    const MAX_LEVEL = 3;

    /**
     * Родительская деятельность
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Activity::class, 'parent_id');
    }

    /**
     * Дочерние виды деятельности
     */
    public function children(): HasMany
    {
        return $this->hasMany(Activity::class, 'parent_id');
    }

    /**
     * Все дочерние виды деятельности (рекурсивно)
     */
    public function descendants(): HasMany
    {
        return $this->hasMany(Activity::class, 'parent_id')->with('descendants');
    }

    /**
     * Организации, связанные с этим видом деятельности
     */
    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class);
    }

    /**
     * Получить все ID дочерних элементов (включая текущий)
     */
    public function getAllChildrenIds(): array
    {
        $ids = [$this->id];

        foreach ($this->children as $child) {
            $ids = array_merge($ids, $child->getAllChildrenIds());
        }

        return $ids;
    }


    /**
     * Проверить, можно ли добавить дочерний элемент
     */
    public function canHaveChildren(): bool
    {
        return $this->level < self::MAX_LEVEL;
    }
}
