<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminTicketController extends Controller
{
    public function index()
    {
        $tickets = SupportTicket::latest()->get();
        return view('pages.apps.tickets.index', compact('tickets'));
    }

    public function show(SupportTicket $ticket)
    {
        $ticket->load(['messages.user']);

        return view('pages.apps.tickets.show', compact('ticket'));
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        $request->validate([
            'message' => 'required|string',
            'status' => 'nullable|in:open,in_progress,closed',
        ]);

        SupportTicketMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'is_admin' => true,
            'message' => $request->message,
        ]);

        if ($request->filled('status')) {
            $ticket->update(['status' => $request->status]);
        } else {
            $ticket->update(['status' => 'in_progress']);
        }

        return back()->with('success', 'Reply sent successfully.');
    }
}
