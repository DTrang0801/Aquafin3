<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MateriaalController;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/materialen', [MateriaalController::class, 'index'])->name('materialen');
    Route::get('/materialen/create', [MateriaalController::class, 'create'])->name('materialen.create');
    Route::get('/materialen/beheer', [MateriaalController::class, 'beheer'])->name('materialen.beheer');
    Route::delete('/materialen/{materiaal}', [MateriaalController::class, 'destroy'])->name('materialen.destroy');
    Route::post('/materialen', [MateriaalController::class, 'store'])->name('materialen.store');
    Route::view('/winkelmandje', 'pages.winkelmandje')->name('winkelmandje');
    Route::view('/bestellingen', 'pages.bestellingen')->name('bestellingen');
    Route::view('/weersvoorspelling', 'pages.weersvoorspelling')->name('weersvoorspelling');
    Route::view('/favorieten', 'pages.favorieten')->name('favorieten');
    Route::view('/gebruikers', 'pages.gebruikers')->name('gebruikers')->middleware('role:admin');

    Route::get('/profile', [App\Http\Controllers\Userzone\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\Userzone\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [App\Http\Controllers\Userzone\ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
