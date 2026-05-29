<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with('user', 'role', 'permissions')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        $roles       = Role::all();
        $permissions = Permission::all();
        return view('employees.create', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|string|min:8',
            'phone'      => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'id'            => Str::uuid(),
            'email'         => $validated['email'],
            'password_hash' => Hash::make($validated['password']),
            'full_name'     => $validated['first_name'] . ' ' . $validated['last_name'],
            'phone'         => $validated['phone'] ?? null,
            'created_at'    => now(),
        ]);

        $employee = Employee::create([
            'id'         => Str::uuid(),
            'user_id'    => $user->id,
            'role_id'    => null,
            'active'     => $request->boolean('is_active', true),
            'created_at' => now(),
        ]);

        $permissions = [];
        if ($request->boolean('can_tickets')) {
            $perm = Permission::where('name', 'tickets')->first();
            if ($perm) $permissions[] = $perm->id;
        }
        if ($request->boolean('can_access')) {
            $perm = Permission::where('name', 'access')->first();
            if ($perm) $permissions[] = $perm->id;
        }

        if (!empty($permissions)) {
            $employee->permissions()->sync($permissions);
        }

        return redirect()->route('employees.index')
            ->with('success', 'Empleado registrado correctamente.');
    }

    public function edit(Employee $employee)
    {
        $employee->load('user', 'role', 'permissions');
        $roles       = Role::all();
        $permissions = Permission::all();
        return view('employees.edit', compact('employee', 'roles', 'permissions'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'phone'      => 'nullable|string|max:20',
        ]);

        $employee->user->update([
            'full_name' => $validated['first_name'] . ' ' . $validated['last_name'],
            'phone'     => $validated['phone'] ?? null,
        ]);

        $employee->update([
            'active' => $request->boolean('is_active'),
        ]);

        $permissions = [];
        if ($request->boolean('can_tickets')) {
            $perm = Permission::where('name', 'tickets')->first();
            if ($perm) $permissions[] = $perm->id;
        }
        if ($request->boolean('can_access')) {
            $perm = Permission::where('name', 'access')->first();
            if ($perm) $permissions[] = $perm->id;
        }

        $employee->permissions()->sync($permissions);

        return redirect()->route('employees.index')
            ->with('success', 'Empleado actualizado correctamente.');
    }

    public function toggleActive(Employee $employee)
    {
        $employee->update(['active' => !$employee->active]);
        $status = $employee->active ? 'activado' : 'desactivado';
        return back()->with('success', "Empleado {$status}.");
    }

    public function destroy(Employee $employee)
    {
        try {
            $user = $employee->user;
            $employee->permissions()->detach();
            $employee->delete();
            if ($user) {
                $user->delete();
            }
            return redirect()->route('employees.index')
                ->with('success', 'Empleado eliminado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('employees.index')
                ->with('error', 'No se puede eliminar este empleado.');
        }
    }
}