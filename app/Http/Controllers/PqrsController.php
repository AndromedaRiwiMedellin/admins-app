<?php

namespace App\Http\Controllers;

use App\Models\Pqrs;
use App\Models\PqrsResponse;
use App\Models\Notification;
use App\Mail\PqrsResponseMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PqrsController extends Controller
{
    public function index(Request $request)
    {
        $query = Pqrs::with('user', 'responses')
            ->orderByDesc('created_at');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('subject', 'ilike', "%{$request->search}%")
                  ->orWhere('message', 'ilike', "%{$request->search}%");
            });
        }

        $pqrs = $query->paginate(15)->withQueryString();

        return view('pqrs.index', compact('pqrs'));
    }

    public function show(Pqrs $pqrs)
    {
        $pqrs->load('user', 'responses.employee.user');
        return view('pqrs.show', compact('pqrs'));
    }

    public function respond(Request $request, Pqrs $pqrs)
    {
        $validated = $request->validate([
            'response' => 'required|string|min:5',
            'status'   => 'required|in:pending,in_process,resolved',
        ]);

        $employee = auth()->user()->employee;

        PqrsResponse::create([
            'id'          => Str::uuid(),
            'pqrs_id'     => $pqrs->id,
            'employee_id' => $employee?->id,
            'response'    => $validated['response'],
            'created_at'  => now(),
        ]);

        $pqrs->update(['status' => $validated['status']]);

        // Notificar al usuario en BD
        Notification::create([
            'id'         => Str::uuid(),
            'user_id'    => $pqrs->user_id,
            'title'      => 'Respuesta a tu solicitud',
            'message'    => 'Tu solicitud "' . $pqrs->subject . '" ha recibido una respuesta.',
            'read'       => false,
            'created_at' => now(),
        ]);

        // Enviar email al usuario
        if ($pqrs->user) {
            Mail::to($pqrs->user->email)
                ->send(new PqrsResponseMail($pqrs, $validated['response']));
        }

        return back()->with('success', 'Respuesta enviada correctamente.');
    }

    public function updateStatus(Request $request, Pqrs $pqrs)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_process,resolved',
        ]);

        $pqrs->update(['status' => $validated['status']]);

        return back()->with('success', 'Estado actualizado.');
    }
}