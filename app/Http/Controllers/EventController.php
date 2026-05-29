<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Favorite;
use App\Models\Notification;
use App\Mail\EventUpdatedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EventController extends Controller
{
    public function index()
    {
        $stats = [
            'total'    => Event::count(),
            'active'   => Event::where('sale_start', '<=', now())
                            ->where('sale_end', '>=', now())->count(),
            'upcoming' => Event::where('event_date', '>=', now())->count(),
            'past'     => Event::where('event_date', '<', now())->count(),
        ];

        $events = Event::orderByDesc('event_date')->paginate(12);

        return view('events.index', compact('events', 'stats'));
    }

    public function create()
    {
        return view('events.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date'  => 'required|date',
            'event_time'  => 'required',
            'sale_start'  => 'required|date',
            'sale_end'    => 'required|date|after:sale_start',
            'capacity'    => 'required|integer|min:1',
            'poster'      => 'nullable|image|max:2048',
        ]);

        $posterUrl = null;
        if ($request->hasFile('poster')) {
            $posterUrl = $request->file('poster')->store('posters', 'public');
        }

        $employee = auth()->user()->employee;

        Event::create([
            'id'             => Str::uuid(),
            'title'          => $validated['title'],
            'description'    => $validated['description'] ?? null,
            'event_date'     => $validated['event_date'] . ' ' . $validated['event_time'],
            'sale_start'     => $validated['sale_start'],
            'sale_end'       => $validated['sale_end'],
            'total_capacity' => $validated['capacity'],
            'poster_url'     => $posterUrl,
            'created_by'     => $employee?->id,
            'created_at'     => now(),
        ]);

        return redirect()->route('events.index')
            ->with('success', 'Evento creado correctamente.');
    }

    public function edit(Event $event)
    {
        return view('events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date'  => 'required|date',
            'event_time'  => 'nullable',
            'sale_start'  => 'required|date',
            'sale_end'    => 'required|date|after:sale_start',
            'capacity'    => 'required|integer|min:1',
            'poster'      => 'nullable|image|max:2048',
        ]);

        $changed = $event->title !== $validated['title'] ||
                   \Carbon\Carbon::parse($event->event_date)->toDateString() !== \Carbon\Carbon::parse($validated['event_date'])->toDateString() ||
                   \Carbon\Carbon::parse($event->sale_start)->toDateTimeString() !== \Carbon\Carbon::parse($validated['sale_start'])->toDateTimeString() ||
                   \Carbon\Carbon::parse($event->sale_end)->toDateTimeString() !== \Carbon\Carbon::parse($validated['sale_end'])->toDateTimeString();

        $posterUrl = $event->poster_url;
        if ($request->hasFile('poster')) {
            if ($event->poster_url) {
                Storage::disk('public')->delete($event->poster_url);
            }
            $posterUrl = $request->file('poster')->store('posters', 'public');
        }

        $eventDate = $validated['event_date'];
        if ($request->filled('event_time')) {
            $eventDate .= ' ' . $validated['event_time'];
        }

        $event->update([
            'title'          => $validated['title'],
            'description'    => $validated['description'] ?? null,
            'event_date'     => $eventDate,
            'sale_start'     => $validated['sale_start'],
            'sale_end'       => $validated['sale_end'],
            'total_capacity' => $validated['capacity'],
            'poster_url'     => $posterUrl,
        ]);

        if ($changed) {
            $this->notifyFavoriteUsers($event);
        }

        return redirect()->route('events.index')
            ->with('success', 'Evento actualizado correctamente.');
    }

    public function destroy(Event $event)
    {
        if ($event->poster_url) {
            Storage::disk('public')->delete($event->poster_url);
        }

        try {
            $event->delete();
        } catch (\Exception $e) {
            return redirect()->route('events.index')
                ->with('error', 'No se puede eliminar un evento con boletas vendidas.');
        }

        return redirect()->route('events.index')
            ->with('success', 'Evento eliminado.');
    }

    public function show(Event $event)
    {
        $event->load('sections', 'tickets');
        return view('events.show', compact('event'));
    }

    private function notifyFavoriteUsers(Event $event): void
    {
        $favorites = Favorite::where('event_id', $event->id)
            ->with('user')
            ->get();

        foreach ($favorites as $favorite) {
            if (!$favorite->user) continue;

            Notification::create([
                'id'         => Str::uuid(),
                'user_id'    => $favorite->user_id,
                'title'      => 'Actualización: ' . $event->title,
                'message'    => 'El evento "' . $event->title . '" ha sido actualizado. Revisa los nuevos detalles.',
                'read'       => false,
                'created_at' => now(),
            ]);

            Mail::to($favorite->user->email)
                ->send(new EventUpdatedMail($event, $favorite->user));
        }
    }
}