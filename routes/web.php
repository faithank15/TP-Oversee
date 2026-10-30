<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockMovementController;
use Illuminate\Support\Facades\Route;

// La racine renvoie vers le tableau de bord (ou vers /login si non connecté)
Route::redirect('/', '/dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('products-trash', [ProductController::class, 'trash'])->name('products.trash');
    Route::patch('products/{id}/restore', [ProductController::class, 'restore'])->name('products.restore');
    Route::resource('products', ProductController::class);

    Route::resource('categories', CategoryController::class)->except('show');

    // Historique immuable : seulement lister, afficher le formulaire, enregistrer
    Route::resource('movements', StockMovementController::class)->only(['index', 'create', 'store']);
});

// /login, /register et /logout : ajoutés par Fortify, rien à écrire ici
