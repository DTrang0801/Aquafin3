<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\MateriaalController;
use App\Http\Controllers\Userzone\ProfileController;
use App\Http\Controllers\WeatherController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

// Admin gebruikersbeheer
Route::middleware('role:admin')->group(function () {
    Route::get('/gebruikers', [UserController::class, 'index'])->name('gebruikers');
    Route::get('/gebruikers/create', [UserController::class, 'create'])->name('gebruikers.create');
    Route::post('/gebruikers', [UserController::class, 'store'])->name('gebruikers.store');
    Route::get('/gebruikers/{user}/edit', [UserController::class, 'edit'])->name('gebruikers.edit');
    Route::put('/gebruikers/{user}', [UserController::class, 'update'])->name('gebruikers.update');
    Route::delete('/gebruikers/{user}', [UserController::class, 'destroy'])->name('gebruikers.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/materialen', [MateriaalController::class, 'index'])->name('materialen');
    Route::get('/materialen/create', [MateriaalController::class, 'create'])->name('materialen.create');
    Route::get('/materialen/beheer', [MateriaalController::class, 'beheer'])->name('materialen.beheer');
    Route::delete('/materialen/{materiaal}', [MateriaalController::class, 'destroy'])->name('materialen.destroy');
    Route::post('/materialen', [MateriaalController::class, 'store'])->name('materialen.store');

    Route::view('/winkelmandje', 'pages.winkelmandje')->name('winkelmandje');

    Route::view('/bestellingen', 'pages.bestellingen')->name('bestellingen');

    Route::get('/weersvoorspelling', [WeatherController::class, 'index'])->name('weersvoorspelling');
    Route::middleware('role:stockbeheerder')->group(function () {
        Route::post('/weersvoorspelling/belangrijk', [WeatherController::class, 'storeBelangrijk'])->name('weersvoorspelling.store');
        Route::post('/weersvoorspelling/simulatie', [WeatherController::class, 'toggleSimulation'])->name('weersvoorspelling.simulate');
        Route::post('/weersvoorspelling/materiaal', [WeatherController::class, 'addMaterial'])->name('weersvoorspelling.addMaterial');
    });

    Route::view('/favorieten', 'pages.favorieten')->name('favorieten');

    // Mag ik deze lijn verwijderen??? - Titi
    // Route::view('/gebruikers', 'pages.gebruikers')->name('gebruikers')->middleware('role:admin');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
