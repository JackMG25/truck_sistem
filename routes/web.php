<?php

use App\Http\Controllers\AgenciaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FleteController;
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
    Route::get('servicios/{servicio}/pagos', [ServicioController::class, 'pagos'])->name('servicios.pagos');
    Route::post('servicios/{servicio}/pagos', [ServicioController::class, 'storePago'])->name('servicios.pagos.store');
    Route::get('fletes/{flete}/download', [FleteController::class, 'download'])->name('fletes.download');
    Route::get('fletes/{flete}/pagos', [FleteController::class, 'pagos'])->name('fletes.pagos');
    Route::post('fletes/{flete}/pagos', [FleteController::class, 'storePago'])->name('fletes.pagos.store');
    Route::delete('fletes/{flete}/pagos/{pago}', [FleteController::class, 'destroyPago'])->name('fletes.pagos.destroy');
    Route::resource('fletes', FleteController::class)->except(['show']);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
