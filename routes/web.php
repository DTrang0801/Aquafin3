<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MateriaalController;

Route::get('/', function () {
    return view('hello');
})->name('home');

Route::permanentRedirect('/hello', '/');

Route::middleware('auth')->group(function () {
    Route::get('/materialen', [MateriaalController::class, 'index'])->name('materialen');
    Route::view('/winkelmandje', 'pages.winkelmandje')->name('winkelmandje');
    Route::view('/bestellingen', 'pages.bestellingen')->name('bestellingen');
    Route::view('/weersvoorspelling', 'pages.weersvoorspelling')->name('weersvoorspelling');
    Route::view('/favorieten', 'pages.favorieten')->name('favorieten');
    Route::view('/gebruikers', 'pages.gebruikers')->name('gebruikers');

    Route::get('/profile', [App\Http\Controllers\Userzone\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\Userzone\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [App\Http\Controllers\Userzone\ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
