<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrganizationResource;
use App\Models\Activity;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class OrganizationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/organizations",
     *     tags={"Organizations"},
     *     summary="Получить список организаций с фильтрами",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *          name="building_id",
     *          in="query",
     *          description="ID здания",
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *          name="activity_id",
     *          in="query",
     *          description="ID деятельности",
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *          name="geo_type",
     *          in="query",
     *          description="Тип гео-поиска (принимает значения 'box' или 'radius')",
     *          required=false,
     *          @OA\Schema(type="string", format="string", example="radius")
     *     ),
     *     @OA\Parameter(
     *          name="lat",
     *          in="query",
     *          description="Широта, если указан geo_type 'box' или 'radius'",
     *          required=false,
     *          @OA\Schema(type="number", format="float", example=55.7558)
     *      ),
     *     @OA\Parameter(
     *          name="lng",
     *          in="query",
     *          description="Долгота, если указан geo_type 'box' или 'radius'",
     *          required=false,
     *          @OA\Schema(type="number", format="float", example=37.6173)
     *      ),
     *     @OA\Parameter(
     *           name="lat_2",
     *           in="query",
     *           description="Широта второй точки, если указан geo_type 'box'",
     *           required=false,
     *           @OA\Schema(type="number", format="float", example=55.7558)
     *       ),
     *      @OA\Parameter(
     *           name="lng_2",
     *           in="query",
     *           description="Долгота второй точки, если указан geo_type 'box'",
     *           required=false,
     *           @OA\Schema(type="number", format="float", example=37.6173)
     *       ),
     *      @OA\Parameter(
     *           name="radius",
     *           in="query",
     *           description="Радиус поиска в метрах (по умолчанию 1000м), если указан geo_type 'radius'",
     *           required=false,
     *           @OA\Schema(type="integer", example=1000)
     *       ),
     *      @OA\Parameter(
     *           name="search",
     *           in="query",
     *           description="Поисковый запрос по названию организации",
     *           required=false,
     *           @OA\Schema(type="string", example="кафе")
     *       ),
     *      @OA\Parameter(
     *           name="per_page",
     *           in="query",
     *           description="Количество результатов на странице (по умолчанию 10)",
     *           required=false,
     *           @OA\Schema(type="integer", example=10)
     *       ),
     *      @OA\Parameter(
     *           name="page",
     *           in="query",
     *           description="Номер страницы",
     *           required=false,
     *           @OA\Schema(type="integer", example=1)
     *       ),
     *     @OA\Response(response=200, description="Список организаций"),
     * )
     */
    public function index(Request $request)
    {
        // Валидация входных данных
        $validated = $request->validate([
            'building_id' => 'nullable|integer|exists:buildings,id',
            'activity_id' => 'nullable|integer|exists:activities,id',
            'geo_type' => ['nullable', Rule::in(['box', 'radius'])],
            'lat' => 'nullable|numeric|between:-90,90',
            'lng' => 'nullable|numeric|between:-180,180',
            'lat_2' => 'nullable|numeric|between:-90,90',
            'lng_2' => 'nullable|numeric|between:-180,180',
            'radius' => 'nullable|integer|min:1|max:50000',
            'search' => 'nullable|string|max:255',
        ]);

        // Параметры поиска
        $searchQuery = $validated['search'] ?? '';

        $filters = [];
        // Геофильтр
        $geoFilter = $this->getGeoFilter($validated);
        if ($geoFilter) {
            $filters[] = $geoFilter;
        }

        // Фильтр по зданию
        if (!empty($validated['building_id'])) {
            $filters[] = "building_id = {$validated['building_id']}";
        }

        // Фильтр по деятельности
        if (!empty($validated['activity_id'])) {
            $activity = Activity::find($validated['activity_id']);
            $ids = implode(', ', $activity->getAllChildrenIds());
            $filters[] = "activity_ids IN [$ids]";
        }

        // Создаем поисковый запрос
        $search = Organization::search($searchQuery, function ($meilisearch, string $query, array $options) use ($filters) {
            if ($filters) {
                $options['filter'] = $filters;
            }
            return $meilisearch->search($query, $options);
        });

        // Выполняем поиск с пагинацией
        $results = $search->paginate(20);

        // Получаем полные модели с отношениями
        $organizationIds = $results->pluck('id');
        $organizations = Organization::whereIn('id', $organizationIds)
            ->get()
            ->keyBy('id');

        // Сортируем результаты в том же порядке, что и в поиске
        $sortedOrganizations = $results->map(function ($result) use ($organizations) {
            return $organizations->get($result->id);
        })->filter();

        // Формируем ответ с пагинацией (используем встроенную пагинацию Scout)
        $paginatedResults = $results->setCollection($sortedOrganizations);

        return OrganizationResource::collection($paginatedResults);
    }

    /**
     * @OA\Get(
     *     path="/organizations/{id}",
     *     tags={"Organizations"},
     *     summary="Получить информацию об организации по ID",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID организации",
     *          required=true,
     *          @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Информация об организации"),
     *     @OA\Response(response=404, description="Организация не найдена"),
     * )
     */
    public function show($id)
    {
        $organization = Organization::with(['building', 'activities', 'phone_numbers'])->find($id);

        if (!$organization) {
            return response()->json(['message' => 'Организация не найдена'], 404);
        }

        return new OrganizationResource($organization);
    }

    /**
     * Получить фильтры для геопоиска
     */
    private function getGeoFilter(array $validated): string
    {
        if (empty($validated['geo_type'])) {
            return "";
        }

        return match($validated['geo_type']) {
            'radius' => $this->buildRadiusFilter($validated),
            'box' => $this->buildBoxFilter($validated),
            default => ""
        };
    }

    private function buildRadiusFilter(array $validated)
    {
        $lat = $validated['lat'] ?? null;
        $lng = $validated['lng'] ?? null;

        if (!$lat || !$lng) {
            return "";
        }

        $radius = $validated['radius'] ?? 1000; // по умолчанию 1км

        return "_geoRadius({$lat}, {$lng}, {$radius})";
    }

    private function buildBoxFilter(array $validated)
    {
        $lat = $validated['lat'] ?? null;
        $lng = $validated['lng'] ?? null;
        $lat2 = $validated['lat_2'] ?? null;
        $lng2 = $validated['lng_2'] ?? null;

        if (!$lat || !$lng || !$lat2 || !$lng2) {
            return "";
        }

        // Определяем границы прямоугольника(нормализуем)
        $topLat = max($lat, $lat2);
        $bottomLat = min($lat, $lat2);
        $leftLng = max($lng, $lng2);
        $rightLng = min($lng, $lng2);

        return "_geoBoundingBox([$topLat, $leftLng], [$bottomLat, $rightLng])";
    }
}
