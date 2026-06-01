<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Pqrs;
use App\Models\Ticket;
use App\Models\User;

class DashboardController extends Controller
{
    private const SOLD_STATUSES = ['VALID', 'USED', 'SOLD'];

    public function index()
    {
        $stats = [
            'events_this_month' => Event::whereMonth('event_date', now()->month)->count(),
            'tickets_this_week' => Ticket::where('purchased_at', '>=', now()->startOfWeek())
                                        ->whereRaw('UPPER(status) IN (?, ?, ?)', self::SOLD_STATUSES)->count(),
            'pending_pqrs'      => Pqrs::where('status', 'pending')->count(),
            'total_users'       => User::count(),
        ];

        $upcomingEvents = Event::where('event_date', '>=', now())
            ->orderBy('event_date')
            ->limit(5)
            ->get();

        $recentPqrs = Pqrs::with('user')
            ->where('status', 'pending')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact('stats', 'upcomingEvents', 'recentPqrs'));
    }
}
