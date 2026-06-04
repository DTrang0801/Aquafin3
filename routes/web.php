<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MateriaalController;

Route::get('/', function () {
    return view('home');
})->name('home');

// Admin gebruikersbeheer
Route::middleware('role:admin')->group(function () {
    Route::get('/gebruikers', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('gebruikers');
    Route::get('/gebruikers/create', [\App\Http\Controllers\Admin\UserController::class, 'create'])->name('gebruikers.create');
    Route::post('/gebruikers', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('gebruikers.store');
    Route::get('/gebruikers/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('gebruikers.edit');
    Route::put('/gebruikers/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('gebruikers.update');
    Route::delete('/gebruikers/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('gebruikers.destroy');
});


Route::middleware('auth')->group(function () {
    Route::get('/materialen', [MateriaalController::class, 'index'])->name('materialen');
    Route::get('/materialen/create', [MateriaalController::class, 'create'])->name('materialen.create');
    Route::post('/materialen', [MateriaalController::class, 'store'])->name('materialen.store');
    Route::view('/winkelmandje', 'pages.winkelmandje')->name('winkelmandje');
    Route::view('/bestellingen', 'pages.bestellingen')->name('bestellingen');
    Route::view('/weersvoorspelling', 'pages.weersvoorspelling')->name('weersvoorspelling');
    Route::view('/favorieten', 'pages.favorieten')->name('favorieten');

    // Mag ik deze lijn verwijderen??? - Titi
   // Route::view('/gebruikers', 'pages.gebruikers')->name('gebruikers')->middleware('role:admin');

    Route::get('/profile', [App\Http\Controllers\Userzone\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\Userzone\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [App\Http\Controllers\Userzone\ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
