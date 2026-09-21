<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;

Route::get('/tickets', [TicketController::class, 'index'])
    ->name('tickets.index');

Route::get('/tickets/{ticket}', [TicketController::class, 'show'])
    ->whereNumber('ticket')
    ->name('tickets.show');

Route::get('/api/tickets/{ticket}', [TicketController::class, 'showJson'])
    ->whereNumber('ticket')
    ->name('tickets.show-json');
// Pola constraint untuk memastikan {ticket} berupa angka
Route::pattern('ticket', '[0-9]+');

// Resource route untuk 7 aksi CRUD tiket
Route::resource('tickets', TicketController::class);
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
