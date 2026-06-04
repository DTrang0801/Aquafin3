<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\MateriaalController;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/materialen', [MateriaalController::class, 'index'])->name('materialen');
    
    Route::get('/winkelmandje', [CartController::class, 'index'])->name('winkelmandje.index');
    Route::post('/winkelmandje/voeg-toe', [CartController::class, 'add'])->name('winkelmandje.add');
    Route::patch('/winkelmandje/update/{id}', [CartController::class, 'update'])->name('winkelmandje.update');
    Route::delete('/winkelmandje/verwijder/{id}', [CartController::class, 'destroy'])->name('winkelmandje.destroy');

    Route::view('/bestellingen', 'pages.bestellingen')->name('bestellingen');
    Route::view('/weersvoorspelling', 'pages.weersvoorspelling')->name('weersvoorspelling');
    Route::view('/favorieten', 'pages.favorieten')->name('favorieten');
    Route::view('/gebruikers', 'pages.gebruikers')->name('gebruikers')->middleware('role:admin');

    Route::get('/profile', [App\Http\Controllers\Userzone\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\Userzone\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [App\Http\Controllers\Userzone\ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
