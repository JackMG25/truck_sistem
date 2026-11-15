<?php

use App\Http\Controllers\FleteController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('auth')->group(function () {
    Route::redirect('/dashboard', '/fletes')->name('dashboard');

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
