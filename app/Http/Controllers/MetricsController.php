<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MetricsController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to   = $request->input('to', now()->toDateString());

        // Boletas vendidas en el rango
        $ticketsSold = Ticket::whereBetween('purchased_at', [$from, $to])
            ->whereIn('status', ['sold', 'used'])
            ->count();

        // Usuarios nuevos en el rango
        $newUsers = User::whereBetween('created_at', [$from, $to])->count();

        // Total usuarios
        $totalUsers = User::count();

        // Boletas por semana
        $ticketsByWeek = Ticket::select(
                DB::raw("DATE_TRUNC('week', purchased_at) as week"),
                DB::raw('COUNT(*) as total')
            )
            ->whereBetween('purchased_at', [$from, $to])
            ->whereIn('status', ['sold', 'used'])
            ->groupBy('week')
            ->orderBy('week')
            ->get()
            ->map(fn($r) => [
                'week'  => \Carbon\Carbon::parse($r->week)->format('d M'),
                'total' => $r->total,
            ]);

        // Ocupación por evento
        $occupancyData = Event::withCount([
                'tickets as sold_count' => fn($q) => $q->whereIn('status', ['sold', 'used']),
            ])
            ->whereBetween('event_date', [$from, $to])
            ->get()
            ->map(fn($e) => [
                'title'     => $e->title,
                'capacity'  => $e->total_capacity,
                'sold'      => $e->sold_count,
                'occupancy' => $e->total_capacity > 0
                    ? round(($e->sold_count / $e->total_capacity) * 100, 1)
                    : 0,
            ]);

        $avgOccupancy = $occupancyData->avg('occupancy') ?? 0;

        return view('metrics.index', compact(
            'ticketsSold',
            'newUsers',
            'totalUsers',
            'ticketsByWeek',
            'occupancyData',
            'avgOccupancy',
            'from',
            'to'
        ));
    }
}