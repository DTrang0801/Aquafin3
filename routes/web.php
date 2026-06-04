<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\WeatherController;
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
    
    Route::get('/winkelmandje', [CartController::class, 'index'])->name('winkelmandje.index');
    Route::post('/winkelmandje/voeg-toe', [CartController::class, 'add'])->name('winkelmandje.add');
    Route::patch('/winkelmandje/update/{id}', [CartController::class, 'update'])->name('winkelmandje.update');
    Route::delete('/winkelmandje/verwijder/{id}', [CartController::class, 'destroy'])->name('winkelmandje.destroy');
  
    Route::get('/materialen/create', [MateriaalController::class, 'create'])->name('materialen.create');
    Route::get('/materialen/beheer', [MateriaalController::class, 'beheer'])->name('materialen.beheer');
    Route::delete('/materialen/{materiaal}', [MateriaalController::class, 'destroy'])->name('materialen.destroy');
    Route::post('/materialen', [MateriaalController::class, 'store'])->name('materialen.store');

    Route::view('/winkelmandje', 'pages.winkelmandje')->name('winkelmandje');

    Route::view('/bestellingen', 'pages.bestellingen')->name('bestellingen');

    Route::get('/weersvoorspelling', [WeatherController::class, 'index'])->name('weersvoorspelling');   
    Route::post('/weersvoorspelling/belangrijk', [WeatherController::class, 'storeBelangrijk'])->name('weersvoorspelling.store');
    Route::post('/weersvoorspelling/simulatie', [WeatherController::class, 'toggleSimulation'])->name('weersvoorspelling.simulate');

    Route::view('/favorieten', 'pages.favorieten')->name('favorieten');

    // Mag ik deze lijn verwijderen??? - Titi
   // Route::view('/gebruikers', 'pages.gebruikers')->name('gebruikers')->middleware('role:admin');

    Route::get('/profile', [App\Http\Controllers\Userzone\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\Userzone\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [App\Http\Controllers\Userzone\ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
