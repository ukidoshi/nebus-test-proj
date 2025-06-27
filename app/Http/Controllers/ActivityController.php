<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActivityRequest;
use App\Http\Resources\ActivityResource;
use App\Models\Activity;
use Illuminate\Http\Request;

/**
 * @OA\Tag(name="Activities")
 */
class ActivityController extends Controller
{
    /**
     * @OA\Get(
     *     path="/activities",
     *     tags={"Activities"},
     *     summary="Получить все деятельности",
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="List of activities")
     * )
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'per_page' => 'nullable|integer',
            'page' => 'nullable|integer'
        ]);

        return ActivityResource::collection(Activity::with(['parent', 'children'])->paginate(15));
    }

    /**
     * @OA\Post(
     *     path="/activities",
     *     tags={"Activities"},
     *     summary="Создать новую активность",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Строительство"),
     *             @OA\Property(property="parent_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Деятельность создана"),
     *     @OA\Response(response=422, description="Превышен максимальный уровень вложенности (3)")
     * )
     */
    public function store(ActivityRequest $request)
    {
        $vaildated = $request->validated();

        if ($vaildated['parent_id']) {
            if (! Activity::find($vaildated['parent_id'])->canHaveChildren()) return response()->json(['data' => 'Превышен максимальный уровень вложенности (3).'], 422);
        }

        $activity = Activity::create($vaildated);
        return new ActivityResource($activity);
    }

    /**
     * @OA\Put(
     *     path="/activities/{id}",
     *     tags={"Activities"},
     *     summary="Обновить деятельность",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Автомобили"),
     *             @OA\Property(property="parent_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Деятельность обновлена"),
     *     @OA\Response(response=422, description="Превышен максимальный уровень вложенности (3)")
     * )
     */
    public function update(ActivityRequest $request, Activity $activity)
    {
        $validated = $request->validated();

        $parent = Activity::find($validated['parent_id']);
        if ($parent->canHaveChildren()) {
            $activity->update($validated);
        } else {
            return response()->json(['data' => 'Превышен максимальный уровень вложенности (3).'], 422);
        }

        return new ActivityResource($activity);
    }

    /**
     * @OA\Delete(
     *     path="/activities/{id}",
     *     tags={"Activities"},
     *     summary="Удалить деятельность",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Деятельность удалена")
     * )
     */
    public function destroy(Activity $activity)
    {
        $activity->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}

