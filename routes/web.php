<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return view('welcome');
});

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
