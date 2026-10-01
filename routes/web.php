<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PageController;

Route::middleware('guest')->group(function () {
	Route::get('/giris', [AuthController::class, 'show'])->name('login');
	Route::get('/kayit', [AuthController::class, 'show'])->name('register');
	Route::post('/giris', [AuthController::class, 'login'])->middleware('throttle:6,1')->name('login.store');
	Route::post('/kayit', [AuthController::class, 'register'])->name('register.store');
});
Route::post('/cikis', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/hakkimizda', [PageController::class, 'about'])->name('about');
Route::get('/iletisim', [PageController::class, 'contact'])->name('contact');
Route::post('/iletisim', [PageController::class, 'storeContact'])->name('contact.store');
Route::get('/rezervasyon', [PageController::class, 'categories'])->name('categories.index');
Route::get('/rezervasyon/{category}', [PageController::class, 'category'])->name('categories.show');
Route::post('/rezervasyon/{category}', [PageController::class, 'storeReservation'])->name('reservations.store');
