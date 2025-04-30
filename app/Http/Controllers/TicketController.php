<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tickets = Ticket::with('priority', 'requester', 'agent', 'category', 'tags')
            ->orderBy('title', 'desc')
            ->get();
        return response()->json($tickets);
    }
    /**
     * Store a newly created resource in storage.
     */

    public function getOpenTickets()
    {
        $tickets = Ticket::with('priority', 'requester', 'agent', 'category', 'tags')
            ->where('status', 'open')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($tickets);
    }

    public function store(Request $request)
    {
        $request->validate($this->rules());
        $ticket = Ticket::create($request->all());

        if ($request->has('tags')) {
            $ticket->tags()->attach($request->tags);
        }

        return response()->json($ticket, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $ticket = Ticket::with('tags', 'priority', 'category', 'requester', 'agent')
            ->find($id);

        if (!$ticket) {
            return response()->json(['error' => 'Ticket no encontrado'], 404);
        }

        return response()->json($ticket);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);
        $request->validate($this->rules());
        $ticket->update($request->all());

        if ($request->has('tags')) {
            $ticket->tags()->sync($request->tags);
        }

        return response()->json($ticket);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Ticket::destroy($id);
        return response()->json(['mensaje' => 'Ticket eliminado'], 200);
    }

    /**
     * Actualiza solo el estado de un ticket (para el Kanban)
     */
    public function updateStatus(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        $request->validate([
            'status' => ['required', 'in:open,in_progress,closed,on_hold,cancelled'],
        ]);

        $ticket->update(['status' => $request->status]);

        // Devolver el ticket completo con id para que el frontend pueda validar
        return response()->json($ticket);
    }

    /**
     * Cierra un ticket estableciendo su estado a 'closed' y registrando la fecha de cierre
     */
    public function closeTicket($id)
    {
        $ticket = Ticket::findOrFail($id);

        $ticket->status = 'closed';
        $ticket->closed_at = Carbon::now();
        $ticket->save();

        return response()->json([
            'mensaje' => 'Ticket cerrado correctamente',
            'data' => $ticket
        ]);
    }

    public function getUserTickets()
    {
        $user = Auth::user();
        $tickets = Ticket::with('agent:id,name') // solo seleccionamos el id y name del agente
            ->where('requester_id', $user->id)
            ->get();

        return response()->json($tickets, 200);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:10', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:open,in_progress,closed,on_hold,cancelled'],
            'priority_id' => ['required', 'exists:priorities,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'requester_id' => ['required', 'exists:users,id'],
            'agent_id' => ['required', 'exists:users,id'],
            'started_at' => ['required', 'date'],
            'closed_at' => ['nullable', 'date', 'after_or_equal:started_at'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:tags,id']
        ];
    }
}
