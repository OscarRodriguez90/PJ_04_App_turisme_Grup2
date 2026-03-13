<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClienteController;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route((int) auth()->user()->id_rol === 1 ? 'admin.dashboard' : 'cliente.index');
    }

    return view('landing');
})->name('home');

Route::get('/login',   [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',  [AuthController::class, 'login']);

Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/check-username', [AuthController::class, 'checkUsername'])->name('check-username');
Route::get('/check-email',    [AuthController::class, 'checkEmail'])->name('check-email');

Route::middleware(['auth', 'role:cliente'])->group(function () {
    Route::get('/cliente', [ClienteController::class, 'index'])->name('cliente.index');
    Route::post('/cliente/favoritos/{lugar}', [ClienteController::class, 'toggleFavorito'])->name('cliente.favoritos.toggle');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/perfil', [AdminController::class, 'perfil'])->name('admin.perfil');
    Route::post('/admin/perfil', [AdminController::class, 'updatePerfil'])->name('admin.perfil.update');

    Route::get('/admin/lugares', [AdminController::class, 'lugares'])->name('admin.lugares');
    Route::post('/admin/lugares', [AdminController::class, 'storeLugar'])->name('admin.lugares.store');
    Route::put('/admin/lugares/{id}', [AdminController::class, 'updateLugar'])->name('admin.lugares.update');
    Route::delete('/admin/lugares/{id}', [AdminController::class, 'deleteLugar'])->name('admin.lugares.delete');
});

Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('home');
})->middleware('auth')->name('logout');
