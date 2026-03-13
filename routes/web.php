<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login',   [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',  [AuthController::class, 'login']);

Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/check-username', [AuthController::class, 'checkUsername'])->name('check-username');
Route::get('/check-email',    [AuthController::class, 'checkEmail'])->name('check-email');

Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

Route::post('/logout', function () {
    auth()->logout();
    return redirect('/');
})->name('logout');

Route::get('/admin/perfil', [AdminController::class, 'perfil'])->name('admin.perfil');
Route::post('/admin/perfil', [AdminController::class, 'updatePerfil'])->name('admin.perfil.update');

Route::get('/admin/lugares', [AdminController::class, 'lugares'])->name('admin.lugares');
Route::post('/admin/lugares', [AdminController::class, 'storeLugar'])->name('admin.lugares.store');
Route::put('/admin/lugares/{id}', [AdminController::class, 'updateLugar'])->name('admin.lugares.update');
Route::delete('/admin/lugares/{id}', [AdminController::class, 'deleteLugar'])->name('admin.lugares.delete');

// Rutas de Salas (Gimcanas)
Route::get('/admin/salas', [AdminController::class, 'salas'])->name('admin.salas');
Route::post('/admin/salas', [AdminController::class, 'storeSala'])->name('admin.salas.store');
Route::put('/admin/salas/{id}', [AdminController::class, 'updateSala'])->name('admin.salas.update');
Route::delete('/admin/salas/{id}', [AdminController::class, 'deleteSala'])->name('admin.salas.delete');
