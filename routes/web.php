<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PqrsController;
use App\Http\Controllers\MetricsController;
use Illuminate\Support\Facades\Route;

// Login
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Panel protegido — solo admins autenticados
Route::middleware('admin')->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Eventos
    Route::resource('events', EventController::class);

    // PQRS
    Route::get('/pqrs', [PqrsController::class, 'index'])->name('pqrs.index');
    Route::get('/pqrs/{pqrs}', [PqrsController::class, 'show'])->name('pqrs.show');
    Route::post('/pqrs/{pqrs}/respond', [PqrsController::class, 'respond'])->name('pqrs.respond');
    Route::patch('/pqrs/{pqrs}/status', [PqrsController::class, 'updateStatus'])->name('pqrs.status');

    // Empleados
    Route::resource('employees', EmployeeController::class)->except(['show']);
    Route::patch('/employees/{employee}/toggle', [EmployeeController::class, 'toggleActive'])->name('employees.toggle');

    // Métricas
    Route::get('/metrics', [MetricsController::class, 'index'])->name('metrics.index');

});