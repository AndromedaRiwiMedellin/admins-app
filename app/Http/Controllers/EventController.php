<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Favorite;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::orderByDesc('event_date')->paginate(12);
        return view('events.index', compact('events'));
    }

    public function create()
    {
        return view('events.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'event_date'     => 'required|date',
            'sale_start'     => 'required|date',
            'sale_end'       => 'required|date|after:sale_start',
            'total_capacity' => 'required|integer|min:1',
            'poster'         => 'nullable|image|max:2048',
        ]);

        $posterUrl = null;
        if ($request->hasFile('poster')) {
            $posterUrl = $request->file('poster')->store('posters', 'public');
        }

        Event::create([
            'id'             => Str::uuid(),
            'title'          => $validated['title'],
            'description'    => $validated['description'] ?? null,
            'event_date'     => $validated['event_date'],
            'sale_start'     => $validated['sale_start'],
            'sale_end'       => $validated['sale_end'],
            'total_capacity' => $validated['total_capacity'],
            'poster_url'     => $posterUrl,
            'created_by'     => auth()->id(),
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
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'event_date'     => 'required|date',
            'sale_start'     => 'required|date',
            'sale_end'       => 'required|date|after:sale_start',
            'total_capacity' => 'required|integer|min:1',
            'poster'         => 'nullable|image|max:2048',
        ]);

        // Detectar si cambió algo importante para notificar favoritos
        $changed = $event->title        !== $validated['title'] ||
                   $event->event_date   !== $validated['event_date'] ||
                   $event->sale_start   !== $validated['sale_start'] ||
                   $event->sale_end     !== $validated['sale_end'];

        if ($request->hasFile('poster')) {
            if ($event->poster_url) {
                Storage::disk('public')->delete($event->poster_url);
            }
            $validated['poster_url'] = $request->file('poster')->store('posters', 'public');
        }

        $event->update($validated);

        // Notificar a usuarios que tienen el evento en favoritos
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
        $event->delete();

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
        }
    }
}