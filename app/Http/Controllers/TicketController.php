<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Priority;
use App\Models\Tag;
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

    public function create(Request $request)
    {
        $validated = $request->validate($this->rules());

        $ticket = Ticket::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'status' => 'open',
            'priority_id' => $validated['priority_id'],
            'category_id' => $validated['category_id'],
            'requester_id' => Auth::id(),
            'started_at' => now(),
        ]);

        // Adjuntar tags
        if (!empty($validated['tags'])) {
            $ticket->tags()->attach($validated['tags']);
        }

        // Procesar archivos con mejor organización
        if ($request->hasFile('attachments')) {
            $ticketDirectory = 'tickets/attachments/' . $ticket->id;

            foreach ($request->file('attachments') as $file) {
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs($ticketDirectory, $fileName, 'public');

                $ticket->attachments()->create([
                    'file_path' => $filePath,
                    'ticket_id' => $ticket->id,
                    'uploaded_by' => Auth::id(),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Ticket creado exitosamente',
            'data' => $ticket->load(['priority', 'category', 'tags', 'attachments', 'requester'])
        ], 201);
    }

    public function getReferenceData()
    {
        return response()->json([
            'categories' => Category::select('id', 'name')->orderBy('name')->get(),
            'priorities' => Priority::select('id', 'name')->orderBy('name')->get(),
            'tags' => Tag::select('id', 'name')->orderBy('name')->get()
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $ticket = Ticket::with('tags', 'priority', 'category', 'requester', 'agent', 'attachments')
            ->findOrFail($id);

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
        $tickets = Ticket::with('agent:id,name')
            ->where('requester_id', $user->id)
            ->get();

        return response()->json($tickets, 200);
    }

    private function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:10',
            'priority_id' => 'required|exists:priorities,id',
            'category_id' => 'required|exists:categories,id',
            'tags' => 'required|array|min:1',
            'tags.*' => 'exists:tags,id',
            'attachments' => 'sometimes|array|max:5',
            'attachments.*' => 'file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png,zip,txt'
        ];
    }
}
