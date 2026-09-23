<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\TicketController;
use App\Models\Category;
use App\Models\Ticket;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

// Constraint regex untuk memastikan parameter ID tiket berupa angka
Route::pattern('ticket', '[0-9]+');

Route::prefix('v1')->name('api.v1.')->group(function () {
    // Endpoint Login dengan rate limiter khusus (5x/menit)
    Route::post('auth/login', [AuthController::class, 'login'])
        ->middleware('throttle:api-login')
        ->name('login');

    // Group Route Terproteksi Sanctum & Rate Limiter API V1 (60x/menit)
    Route::middleware(['auth:sanctum', 'throttle:api-v1'])->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('me', [AuthController::class, 'me'])->name('me');

        // Endpoint Referensi Kategori
        Route::get('categories', function () {
            return response()->json([
                'data' => Category::orderBy('name')->get(['id', 'name'])
            ]);
        })->name('categories.index');

        // Endpoint Laporan Ringkasan (Khusus Admin)
        Route::get('reports/summary', function () {
            Gate::authorize('view-ticket-summary');
            return response()->json([
                'data' => ['ticket_count' => Ticket::count()]
            ]);
        })->name('reports.summary');

        // RESTful Resource API Tiket (index, store, show, update, destroy)
        Route::apiResource('tickets', TicketController::class);
    });
});