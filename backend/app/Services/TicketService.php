<?php

namespace App\Services;

use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TicketService
{
    public function create(array $data): Ticket
    {
        return DB::transaction(function () use ($data) {
            $ticket = Ticket::create([
                'user_id' => $data['user_id'],
                'category_id' => $data['category_id'],
                'subject' => $data['subject'],
                'description' => $data['description'],
                'status' => 'open',
                'is_urgent' => (bool) $data['is_urgent'],
            ]);

            $ticket->comments()->create([
                'user_id' => $ticket->user_id,
                'body' => $data['note'],
            ]);

            return $ticket;
        });
    }

    public function update(Ticket $ticket, array $data): Ticket
    {
        return DB::transaction(function () use ($ticket, $data) {
            $current = Ticket::query()->lockForUpdate()->findOrFail($ticket->id);
            $current->update([
                'category_id' => $data['category_id'],
                'subject' => $data['subject'],
                'description' => $data['description'],
                'status' => $data['status'],
                'is_urgent' => (bool) $data['is_urgent'],
            ]);

            $current->comments()->create([
                'user_id' => $current->user_id,
                'body' => $data['note'],
            ]);

            return $current;
        });
    }

    public function delete(Ticket $ticket): void
    {
        DB::transaction(function () use ($ticket) {
            $current = Ticket::query()->lockForUpdate()->findOrFail($ticket->id);
            if ($current->status === 'closed') {
                throw ValidationException::withMessages([
                    'ticket' => 'Tiket closed tidak boleh dihapus.',
                ]);
            }

            $current->delete(); // FK cascade menghapus comments.
        });
    }
}