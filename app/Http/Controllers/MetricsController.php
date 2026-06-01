<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MetricsController extends Controller
{
    private const SOLD_STATUSES = ['VALID', 'USED', 'SOLD'];

    public function index(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->toDateString());
        $fromDate = Carbon::parse($from)->startOfDay();
        $toDate = Carbon::parse($to)->endOfDay();

        $ticketsSold = Ticket::whereBetween('purchased_at', [$fromDate, $toDate])
            ->whereRaw('UPPER(status) IN (?, ?, ?)', self::SOLD_STATUSES)
            ->count();

        $newUsers = User::whereBetween('created_at', [$fromDate, $toDate])->count();
        $totalUsers = User::count();

        $totalRevenue = (float) DB::table('tickets')
            ->leftJoin('area_seats', 'area_seats.ticket_id', '=', 'tickets.id')
            ->leftJoin('event_area', 'event_area.id', '=', 'area_seats.event_area_id')
            ->whereBetween('tickets.purchased_at', [$fromDate, $toDate])
            ->whereRaw('UPPER(tickets.status) IN (?, ?, ?)', self::SOLD_STATUSES)
            ->sum(DB::raw('COALESCE(event_area.price, 0)'));

        $ticketsByWeek = Ticket::select(
                DB::raw("DATE_TRUNC('week', purchased_at) as week"),
                DB::raw('COUNT(*) as total')
            )
            ->whereBetween('purchased_at', [$fromDate, $toDate])
            ->whereRaw('UPPER(status) IN (?, ?, ?)', self::SOLD_STATUSES)
            ->groupBy('week')
            ->orderBy('week')
            ->get()
            ->map(fn($r) => [
                'week' => Carbon::parse($r->week)->format('d M'),
                'total' => $r->total,
            ]);

        $occupancyData = Event::withCount([
                'tickets as sold_count' => fn($q) => $q->whereRaw('UPPER(status) IN (?, ?, ?)', self::SOLD_STATUSES),
            ])
            ->whereBetween('event_date', [$fromDate, $toDate])
            ->get()
            ->map(fn($e) => [
                'title' => $e->title,
                'capacity' => $e->total_capacity,
                'sold' => $e->sold_count,
                'occupancy' => $e->total_capacity > 0
                    ? round(($e->sold_count / $e->total_capacity) * 100, 1)
                    : 0,
            ]);

        $avgOccupancy = $occupancyData->avg('occupancy') ?? 0;

        return view('metrics.index', compact(
            'ticketsSold',
            'newUsers',
            'totalUsers',
            'totalRevenue',
            'ticketsByWeek',
            'occupancyData',
            'avgOccupancy',
            'from',
            'to'
        ));
    }
}
