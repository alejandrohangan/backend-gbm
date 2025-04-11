<?php

namespace App\Http\Controllers;

use App\Models\TicketHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TicketHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ticketHistories = TicketHistory::whereHas('ticket', function ($query) {
            $query->where('created_at', '<', Carbon::now()->subYear());
        })->get();

        return response()->json($ticketHistories);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(TicketHistory $ticketHistory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TicketHistory $ticketHistory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TicketHistory $ticketHistory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TicketHistory $ticketHistory)
    {
        //
    }
}
