<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Cards de estadísticas
        $stats = [
            'openTickets' => Ticket::where('status', 'open')->count(),
            'resolvedTickets' => Ticket::where('status', 'closed')->count(),
            'highPriorityTickets' => Ticket::where('priority', 'high')->count(),
            'activeAgents' => User::where('role', 'agent')->where('is_active', true)->count(),
            'inProgressTickets' => Ticket::where('status', 'in_progress')->count(),
        ];

        // Datos para gráfico de tendencias (últimos 7 días)
        $ticketTrends = Ticket::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as created'),
            DB::raw('SUM(CASE WHEN status = "closed" THEN 1 ELSE 0 END) as resolved')
        )
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Distribución de prioridades
        $priorityDistribution = Ticket::select('priority', DB::raw('COUNT(*) as count'))
            ->groupBy('priority')
            ->get()
            ->map(function ($item) {
                return [
                    'priority' => $item->priority,
                    'count' => $item->count,
                    'percentage' => 0 // Se calculará en el frontend
                ];
            });

        // Tickets recientes
        $recentTickets = Ticket::with(['user', 'assignedUser'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'stats' => $stats,
            'ticketTrends' => $ticketTrends,
            'priorityDistribution' => $priorityDistribution,
            'recentTickets' => $recentTickets,
        ]);
    }
}
