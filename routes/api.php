<?php

use App\Http\Controllers\Api\CarController;
use App\Http\Controllers\Api\DealerController;
use App\Http\Controllers\Api\FilterOptionsController;
use App\Http\Controllers\Api\ManufacturerController;
use Illuminate\Support\Facades\Route;

Route::apiResource('manufacturers', ManufacturerController::class);
Route::apiResource('dealers', DealerController::class);
Route::apiResource('cars', CarController::class);

Route::prefix('filters')->controller(FilterOptionsController::class)->group(function () {
    Route::get('cars', 'cars');
    Route::get('dealers', 'dealers');
});
