<?php

use App\Http\Controllers\SessionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Session login/logout untuk SPA
Route::post('/login', [SessionController::class, 'login'])
    ->middleware('throttle:api-login')
    ->name('login');

Route::post('/logout', [SessionController::class, 'logout'])
    ->middleware('auth:web');

// Route tiket lama dinonaktifkan untuk migrasi ke REST API (Pertemuan 5)
// Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
// Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->whereNumber('ticket')->name('tickets.show');
// Route::get('/api/tickets/{ticket}', [TicketController::class, 'showJson'])->whereNumber('ticket')->name('tickets.show-json');
// Route::resource('tickets', TicketController::class);