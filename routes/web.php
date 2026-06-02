<?php

use Illuminate\Support\Facades\Route;

Route::get('/hello', function () {
    return '<html><body style="background:pink; display:flex; align-items:center; justify-content:center; height:100vh; margin:0; font-family:sans-serif; font-size:3rem;">Hello World</body></html>';
});

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
