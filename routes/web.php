<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PartidaController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'showLoginForm']);
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/iniciarsesion', [AuthController::class, 'login'])->name('iniciarsesion');

Route::middleware('auth')->group(function () {
    Route::get('/cerrarsesion', [AuthController::class, 'cerrarSesion'])->name('cerrarsesion');

Route::middleware(['role:administrador'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/partidas', [AdminController::class, 'partidas'])->name('admin.partidas.index');
    Route::post('/admin/usuarios', [AdminController::class, 'storeUser'])->name('admin.usuarios.store');
    Route::put('/admin/usuarios/{user}', [AdminController::class, 'updateUser'])->name('admin.usuarios.update');
    Route::delete('/admin/usuarios/{user}', [AdminController::class, 'destroyUser'])->name('admin.usuarios.destroy');
    Route::post('/admin/partidas/{id}/cancelar', [AdminController::class, 'cancelarPartida'])->name('admin.partidas.cancelar');
});
    
    Route::middleware(['role:jugador'])->group(function () {
        Route::get('/jugador/lobby', [PartidaController::class, 'lobby'])->name('jugador.lobby');
        Route::post('/jugador/partidas/crear', [PartidaController::class, 'crear'])->name('jugador.partidas.crear');
        Route::post('/jugador/partidas/{id}/unirse', [PartidaController::class, 'unirse'])->name('jugador.partidas.unirse');
        Route::get('/jugador/partidas/{id}', [PartidaController::class, 'show'])->name('jugador.partidas.show');
        Route::post('/jugador/partidas/{id}/mover', [PartidaController::class, 'mover'])->name('jugador.partidas.mover');
        Route::post('/jugador/partidas/{id}/abandonar', [PartidaController::class, 'abandonar'])->name('jugador.partidas.abandonar');
    });

    Route::middleware(['role:administrador,jugador'])->group(function () {
        Route::get('/partidas/{id}/estado', [PartidaController::class, 'estado'])->name('partidas.estado');
    });
});