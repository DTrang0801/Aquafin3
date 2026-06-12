<?php

use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MateriaalController;
use App\Http\Controllers\StockDashboardController;
use App\Http\Controllers\Userzone\ProfileController;
use App\Http\Controllers\WeatherController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Admin gebruikersbeheer
Route::middleware('role:admin')->group(function () {
    Route::get('/gebruikers', [UserController::class, 'index'])->name('gebruikers');
    Route::get('/gebruikers/create', [UserController::class, 'create'])->name('gebruikers.create');
    Route::post('/gebruikers', [UserController::class, 'store'])->name('gebruikers.store');
    Route::get('/gebruikers/{user}/edit', [UserController::class, 'edit'])->name('gebruikers.edit');
    Route::put('/gebruikers/{user}', [UserController::class, 'update'])->name('gebruikers.update');
    Route::delete('/gebruikers/{user}', [UserController::class, 'destroy'])->name('gebruikers.destroy');
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/materialen', [MateriaalController::class, 'index'])->name('materialen');
    Route::get('/materialen/json', [MateriaalController::class, 'getMaterialsJson'])->name('materialen.json');
    Route::get('/materialen/suggesties', [MateriaalController::class, 'suggesties'])->name('materialen.suggesties');

    Route::get('/winkelmandje', [CartController::class, 'index'])->name('winkelmandje.index');
    Route::post('/winkelmandje/voeg-toe', [CartController::class, 'add'])->name('winkelmandje.add');
    Route::patch('/winkelmandje/update/{id}', [CartController::class, 'update'])->name('winkelmandje.update');
    Route::delete('/winkelmandje/verwijder/{id}', [CartController::class, 'destroy'])->name('winkelmandje.destroy');
    Route::get('/winkelmandje/bestellen', [CartController::class, 'checkout'])->name('winkelmandje.checkout');
    Route::post('/winkelmandje/bevestigen', [CartController::class, 'confirmOrder'])->name('winkelmandje.confirm');

    Route::get('/materialen/create', [MateriaalController::class, 'create'])->name('materialen.create');
    Route::get('/materialen/beheer', [MateriaalController::class, 'beheer'])->name('materialen.beheer');
    Route::delete('/materialen/{materiaal}', [MateriaalController::class, 'destroy'])->name('materialen.destroy');
    Route::get('/materialen/{materiaal}/edit', [MateriaalController::class, 'edit'])->name('materialen.edit');
    Route::put('/materialen/{materiaal}', [MateriaalController::class, 'update'])->name('materialen.update');
    Route::post('/materialen', [MateriaalController::class, 'store'])->name('materialen.store');

    Route::middleware('role:technieker')->group(function () {
        Route::post('/home/neerslaggegevens/vernieuwen', [HomeController::class, 'refreshForecast'])->name('home.forecast.refresh');
    });

    Route::get('/bestellingen', [CartController::class, 'indexOrders'])->name('bestellingen');
    Route::get('/bestellingen/{bestelling}/bewerk', [CartController::class, 'editOrder'])->name('bestellingen.edit');
    Route::put('/bestellingen/{bestelling}', [CartController::class, 'updateOrder'])->name('bestellingen.update');
    Route::get('/overzicht', [CartController::class, 'overzicht'])->name('overzicht')->middleware('role:stockbeheerder,admin');

    Route::get('/weersvoorspelling', [WeatherController::class, 'index'])->name('weersvoorspelling');
    Route::middleware('role:stockbeheerder')->group(function () {
        Route::get('/weersvoorspelling/kritieke-items', [WeatherController::class, 'criticalItems'])->name('weersvoorspelling.kritieke-items');
        Route::post('/weersvoorspelling/belangrijk', [WeatherController::class, 'storeBelangrijk'])->name('weersvoorspelling.store');
        Route::post('/weersvoorspelling/simulatie', [WeatherController::class, 'toggleSimulation'])->name('weersvoorspelling.simulate');
        Route::post('/weersvoorspelling/materiaal', [WeatherController::class, 'addMaterial'])->name('weersvoorspelling.addMaterial');
        Route::post('/weersvoorspelling/neerslag', [WeatherController::class, 'storeNeerslag'])->name('weersvoorspelling.storeNeerslag');
        Route::get('/stock-dashboard', [StockDashboardController::class, 'index'])->name('stock-dashboard');
    });

    // Mag ik deze lijn verwijderen??? - Titi
    // Route::view('/gebruikers', 'pages.gebruikers')->name('gebruikers')->middleware('role:admin');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
