<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(): View
    {
        $tickets = Ticket::with(['user', 'category'])
            ->orderByDesc('id')
            ->paginate(10);

        return view('tickets.index', compact('tickets'));
    }

    public function show(Ticket $ticket): View
    {
        $ticket->load(['user', 'category', 'comments.user']);
        return view('tickets.show', compact('ticket'));
    }

    public function showJson(Request $request, Ticket $ticket): JsonResponse
    {
        Log::info('Ticket JSON requested', [
            'ticket_id' => $ticket->id,
            'path' => $request->path(),
        ]);

        $ticket->load(['user', 'category', 'comments.user']);
        return response()->json(['data' => $ticket]);
    }
}