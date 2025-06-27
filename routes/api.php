<?php

use App\Http\Controllers\OrganizationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\OrganizationActivityController;

// Организации
Route::get('/organizations', [OrganizationController::class, 'index']);
Route::get('/organizations/{id}', [OrganizationController::class, 'show']);

// Здания организации


// Виды деятельности
Route::get('/activities', [ActivityController::class, 'index']);
Route::post('/activities', [ActivityController::class, 'store']);
Route::put('/activities/{id}', [ActivityController::class, 'update']);
Route::delete('/activities/{id}', [ActivityController::class, 'destroy']);

// Привязка видов деятельности к организациям
Route::post('/organizations/{organization}/activities/attach', [OrganizationActivityController::class, 'attach']);
Route::post('/organizations/{organization}/activities/detach', [OrganizationActivityController::class, 'detach']);

