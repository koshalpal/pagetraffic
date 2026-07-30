<?php

use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SearchController::class, 'index'])->name('search.index');
Route::post('/search', [SearchController::class, 'search'])->name('search.perform');
Route::get('/search/{batch}', [SearchController::class, 'show'])->name('search.show');
Route::get('/search/{batch}/export', [SearchController::class, 'export'])->name('search.export');
