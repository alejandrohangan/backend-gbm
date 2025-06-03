<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Cards de estadísticas
        $stats = [
            'openTickets' => Ticket::where('status', 'open')->count(),
            'closedTickets' => Ticket::where('status', 'closed')->count(),
            'highPriorityTickets' => Ticket::whereHas('priority', function ($q) {
                $q->where('name', 'Alta');
            })->count(),
            'inProgressTickets' => Ticket::where('status', 'in_progress')->count(),
        ];

        // Distribución de prioridades
        $priorityDistribution = DB::table('priorities')
            ->leftJoin('tickets', 'priorities.id', '=', 'tickets.priority_id')
            ->select('priorities.name as priority', DB::raw('COUNT(tickets.id) as count'))
            ->groupBy('priorities.id', 'priorities.name')
            ->orderBy('count', 'desc')
            ->limit(4)
            ->get()
            ->map(function ($item) {
                return [
                    'priority' => $item->priority,
                    'count' => (int) $item->count
                ];
            });

        $categoryDistribution = Category::leftJoin('tickets', 'categories.id', '=', 'tickets.category_id')
            ->select('categories.name as category', DB::raw('COUNT(tickets.id) as count'))
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('count', 'desc')
            ->limit(4)
            ->get()
            ->map(function ($item) {
                return [
                    'category' => $item->category,
                    'count' => (int) $item->count
                ];
            });

        $recentTickets = Ticket::with(['priority', 'agent'])
            ->select('id', 'title', 'status', 'priority_id', 'agent_id', 'created_at')
            ->latest('created_at')
            ->take(5)
            ->get()
            ->map(function ($ticket) {
                return [
                    'id'       => $ticket->id,
                    'title'    => $ticket->title,
                    'status'   => $ticket->status,
                    'priority' => $ticket->priority->name ?? 'Sin prioridad',
                    'agent'    => $ticket->agent->name ?? 'Sin asignar',
                    'created'  => $ticket->created_at->toDateTimeString(),
                ];
            });

        return response()->json([
            'stats' => $stats,
            'priorityDistribution' => $priorityDistribution,
            'categoryDistribution' => $categoryDistribution,
            'recentTickets' => $recentTickets,
        ]);
    }
}
