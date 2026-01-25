<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
// Healthcheck: Session/Redis に依存しない（StartSession を除外して 500 を防ぐ）
Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
})->withoutMiddleware([\Illuminate\Session\Middleware\StartSession::class]);
require __DIR__.'/auth.php';
