<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MateriaalController;

Route::get('/hello', function () {
    return view('hello');
})->name('hello');

Route::get('/materiaallijst', [MateriaalController::class, 'index'])->name('materialen.index');

Route::permanentRedirect('/', '/hello');

Route::get('/dashboard', function () {
    return view('userzone.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [App\Http\Controllers\Userzone\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\Userzone\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [App\Http\Controllers\Userzone\ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
