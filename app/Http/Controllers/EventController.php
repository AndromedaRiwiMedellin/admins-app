<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventSection;
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
            'title'               => 'required|string|max:255',
            'description'         => 'nullable|string',
            'event_date'          => 'required|date',
            'event_time'          => 'required',
            'sale_start'          => 'required|date',
            'sale_end'            => 'required|date|after:sale_start',
            'capacity'            => 'required|integer|min:1',
            'poster'              => 'nullable|image|max:2048',
            'areas'               => 'nullable|array',
            'areas.*.area_name'   => 'required_with:areas|string|max:100',
            'areas.*.price'       => 'required_with:areas|numeric|min:0',
            'areas.*.capacity'    => 'required_with:areas|integer|min:1',
            'areas.*.description' => 'nullable|string',
        ]);

        try {
            $posterUrl = null;
            if ($request->hasFile('poster')) {
                $posterUrl = $request->file('poster')->store('posters', 'public');
            }

            // Evita romper la base de datos si no hay un empleado autenticado en la sesión
            $employeeId = null;
            if (auth()->check() && auth()->user()->employee) {
                $employeeId = auth()->user()->employee->id;
            }

            $event = Event::create([
                'id'             => (string) Str::uuid(),
                'title'          => $validated['title'],
                'description'    => $validated['description'] ?? null,
                'event_date'     => $validated['event_date'] . ' ' . $validated['event_time'],
                'sale_start'     => $validated['sale_start'],
                'sale_end'       => $validated['sale_end'],
                'total_capacity' => $validated['capacity'],
                'poster_url'     => $posterUrl,
                'created_by'     => $employeeId,
                'created_at'     => now(),
            ]);

            // Guardar áreas de forma segura en lote
            if (!empty($validated['areas'])) {
                foreach ($validated['areas'] as $area) {
                    if (empty($area['area_name'])) continue; // Salta filas vacías accidentales

                    EventSection::create([
                        'event_id'    => $event->id,
                        'area_name'   => $area['area_name'],
                        'price'       => $area['price'],
                        'capacity'    => $area['capacity'],
                        'description' => $area['description'] ?? null,
                    ]);
                }
            }

            return redirect()->route('events.index')
                ->with('success', 'Evento creado correctamente.');

        } catch (\Throwable $e) {
            // En caso de error, volvemos atrás sin tumbar Nginx con un 500
            return redirect()->back()
                ->withInput()
                ->withErrors(['error_db' => 'Error de consistencia en el servidor: ' . $e->getMessage()]);
        }
    }

    public function edit(Event $event)
    {
        $event->load('sections');
        return view('events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title'               => 'required|string|max:255',
            'description'         => 'nullable|string',
            'event_date'          => 'required|date',
            'event_time'          => 'nullable',
            'sale_start'          => 'required|date',
            'sale_end'            => 'required|date|after:sale_start',
            'capacity'            => 'required|integer|min:1',
            'poster'              => 'nullable|image|max:2048',
            'areas'               => 'nullable|array',
            'areas.*.area_name'   => 'required_with:areas|string|max:100',
            'areas.*.price'       => 'required_with:areas|numeric|min:0',
            'areas.*.capacity'    => 'required_with:areas|integer|min:1',
            'areas.*.description' => 'nullable|string',
        ]);

        try {
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

            // Actualizar áreas eliminando las anteriores limpiamente
            if (isset($validated['areas'])) {
                $event->sections()->delete();
                foreach ($validated['areas'] as $area) {
                    if (empty($area['area_name'])) continue;

                    EventSection::create([
                        'event_id'    => $event->id,
                        'area_name'   => $area['area_name'],
                        'price'       => $area['price'],
                        'capacity'    => $area['capacity'],
                        'description' => $area['description'] ?? null,
                    ]);
                }
            }

            if ($changed) {
                $this->notifyFavoriteUsers($event);
            }

            return redirect()->route('events.index')
                ->with('success', 'Evento actualizado correctamente.');

        } catch (\Throwable $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error_db' => 'Error al actualizar el servidor: ' . $e->getMessage()]);
        }
    }

    public function destroy(Event $event)
    {
        if ($event->poster_url) {
            Storage::disk('public')->delete($event->poster_url);
        }

        try {
            $event->delete();
        } catch (\Throwable $e) {
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
        try {
            $favorites = Favorite::where('event_id', $event->id)
                ->with('user')
                ->get();

            foreach ($favorites as $favorite) {
                if (!$favorite->user) continue;

                Notification::create([
                    'id'         => (string) Str::uuid(),
                    'user_id'    => $favorite->user_id,
                    'title'      => 'Actualización: ' . $event->title,
                    'message'    => 'El evento "' . $event->title . '" ha sido actualizado. Revisa los nuevos detalles.',
                    'read'       => false,
                    'created_at' => now(),
                ]);

                Mail::to($favorite->user->email)
                    ->send(new EventUpdatedMail($event, $favorite->user));
            }
        } catch (\Throwable $e) {
            // El fallo del envío de correos no debe interrumpir ni romper el flujo principal
            logger('Error notificando usuarios: ' . $e->getMessage());
        }
    }
}
