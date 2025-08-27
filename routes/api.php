<?php

use App\Http\Controllers\Api\CatController;
use App\Http\Controllers\Api\FeedingController;
use App\Http\Controllers\Api\HealthRecordController;
use App\Http\Controllers\Api\VeterinaryVisitController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Маршрут для получения данных текущего пользователя
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Группа маршрутов для котов
Route::prefix('cats')->group(function () {
    // GET /api/cats - Получить список всех котов текущего пользователя
    Route::get('/', [CatController::class, 'index']);

    // POST /api/cats - Создать нового кота
    Route::post('/', [CatController::class, 'store']);

    // Группа маршрутов для конкретного кота
    Route::prefix('{cat}')->group(function () {
        // GET /api/cats/1 - Получить подробную информацию о коте с ID=1
        Route::get('/', [CatController::class, 'show']);

        // PUT /api/cats/1 - Обновить информацию о коте с ID=1
        Route::put('/', [CatController::class, 'update']);

        // DELETE /api/cats/1 - Удалить кота с ID=1
        Route::delete('/', [CatController::class, 'destroy']);

        // GET /api/cats/1/feedings - Получить историю кормлений кота
        Route::get('/feedings', [FeedingController::class, 'indexByCat']);

        // POST /api/cats/1/feedings - Добавить запись о кормлении коту
        Route::post('/feedings', [FeedingController::class, 'storeForCat']);

        // GET /api/cats/1/feedings/stats/daily/2023-10-27 - Статистика кормлений за день
        Route::get('/feedings/stats/daily/{date}', [FeedingController::class, 'getDailyStats']);

        // GET /api/cats/1/feedings/stats/weekly/2023-10-23 - Статистика кормлений за неделю
        Route::get('/feedings/stats/weekly/{startDate}', [FeedingController::class, 'getWeeklyStats']);

        // GET /api/cats/1/health-records - Получить записи о здоровье кота
        Route::get('/health-records', [HealthRecordController::class, 'indexByCat']);

        // POST /api/cats/1/health-records - Добавить запись о здоровье коту
        Route::post('/health-records', [HealthRecordController::class, 'storeForCat']);

        // GET /api/cats/1/veterinary-visits - Получить ветеринарные визиты кота
        Route::get('/veterinary-visits', [VeterinaryVisitController::class, 'indexByCat']);

        // POST /api/cats/1/veterinary-visits - Добавить ветеринарный визит коту
        Route::post('/veterinary-visits', [VeterinaryVisitController::class, 'storeForCat']);
    });
});

// Группа маршрутов для кормлений
Route::prefix('feedings')->group(function () {
    // GET /api/feedings - Получить все кормления пользователя
    Route::get('/', [FeedingController::class, 'index']);

    // GET /api/feedings/today - Получить сегодняшние кормления
    Route::get('/today', [FeedingController::class, 'getTodayFeedings']);

    // POST /api/feedings - Создать новое кормление
    Route::post('/', [FeedingController::class, 'store']);

    // Группа маршрутов для конкретного кормления
    Route::prefix('{feeding}')->group(function () {
        // GET /api/feedings/1 - Получить информацию о кормлении
        Route::get('/', [FeedingController::class, 'show']);

        // PUT /api/feedings/1 - Обновить информацию о кормлении
        Route::put('/', [FeedingController::class, 'update']);

        // DELETE /api/feedings/1 - Удалить кормление
        Route::delete('/', [FeedingController::class, 'destroy']);
    });
});

// Группа маршрутов для записей о здоровье
Route::prefix('health-records')->group(function () {
    // GET /api/health-records - Получить все записи о здоровье пользователя
    Route::get('/', [HealthRecordController::class, 'index']);

    // POST /api/health-records - Создать новую запись о здоровье
    Route::post('/', [HealthRecordController::class, 'store']);

    // Группа маршрутов для конкретной записи о здоровье
    Route::prefix('{healthRecord}')->group(function () {
        // GET /api/health-records/1 - Получить информацию о записи о здоровье
        Route::get('/', [HealthRecordController::class, 'show']);

        // PUT /api/health-records/1 - Обновить информацию о записи о здоровье
        Route::put('/', [HealthRecordController::class, 'update']);

        // DELETE /api/health-records/1 - Удалить запись о здоровье
        Route::delete('/', [HealthRecordController::class, 'destroy']);
    });
});

// Группа маршрутов для ветеринарных визитов
Route::prefix('veterinary-visits')->group(function () {
    // GET /api/veterinary-visits - Получить все ветеринарные визиты пользователя
    Route::get('/', [VeterinaryVisitController::class, 'index']);

    // POST /api/veterinary-visits - Создать новый ветеринарный визит
    Route::post('/', [VeterinaryVisitController::class, 'store']);

    // Группа маршрутов для конкретного ветеринарного визита
    Route::prefix('{veterinaryVisit}')->group(function () {
        // GET /api/veterinary-visits/1 - Получить информацию о ветеринарном визите
        Route::get('/', [VeterinaryVisitController::class, 'show']);

        // PUT /api/veterinary-visits/1 - Обновить информацию о ветеринарном визите
        Route::put('/', [VeterinaryVisitController::class, 'update']);

        // DELETE /api/veterinary-visits/1 - Удалить ветеринарный визит
        Route::delete('/', [VeterinaryVisitController::class, 'destroy']);
    });
});

// Дополнительные маршруты для статистики и отчетов
Route::prefix('reports')->group(function () {
    // GET /api/reports/feedings/today - Сегодняшние кормления (альтернативный путь)
    Route::get('/feedings/today', [FeedingController::class, 'getTodayFeedings']);

    // GET /api/reports/health/stats/1 - Статистика здоровья кота
    Route::get('/health/stats/{cat}', [HealthRecordController::class, 'getStats']);

    // GET /api/reports/veterinary/upcoming - Предстоящие визиты к ветеринару
    Route::get('/veterinary/upcoming', [VeterinaryVisitController::class, 'getUpcomingVisits']);

    // GET /api/reports/veterinary/stats - Статистика ветеринарных визитов
    Route::get('/veterinary/stats', [VeterinaryVisitController::class, 'getStats']);
});

// Fallback route for undefined API endpoints
Route::fallback(function () {
    return response()->json([
        'message' => 'API endpoint not found. Please check the documentation.',
        'status' => 404
    ], 404);
});
