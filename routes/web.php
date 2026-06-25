<?php

use App\Http\Controllers\AgenciaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServicioController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::resource('agencias', AgenciaController::class)->except(['show']);
    Route::resource('clientes', ClienteController::class)->except(['show']);
    Route::resource('servicios', ServicioController::class)->except(['show']);
    Route::post('servicios/clientes-inline', [ServicioController::class, 'storeClienteInline'])->name('servicios.clientes-inline');
    Route::post('servicios/agencias-inline', [ServicioController::class, 'storeAgenciaInline'])->name('servicios.agencias-inline');
    Route::post('servicios/{servicio}/pagos-inline', [ServicioController::class, 'storePagoInline'])->name('servicios.pagos-inline');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
