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
Route::get('/yonetim/giris', [App\Http\Controllers\AdminController::class, 'showLogin'])->name('admin.login');
Route::post('/yonetim/giris', [App\Http\Controllers\AdminController::class, 'login'])->middleware('throttle:6,1')->name('admin.login.store');
Route::post('/yonetim/cikis', [App\Http\Controllers\AdminController::class, 'logout'])->middleware('auth')->name('admin.logout');

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/hakkimizda', [PageController::class, 'about'])->name('about');
Route::get('/iletisim', [PageController::class, 'contact'])->name('contact');
Route::post('/iletisim', [PageController::class, 'storeContact'])->middleware('auth')->name('contact.store');
Route::get('/rezervasyon', [PageController::class, 'categories'])->name('categories.index');
Route::get('/rezervasyon/{category}', [PageController::class, 'category'])->name('categories.show');
Route::middleware('auth')->group(function () {
	Route::post('/rezervasyon/{category}', [PageController::class, 'storeReservation'])->name('reservations.store');
	Route::get('/hesabim/rezervasyonlar', [App\Http\Controllers\CustomerController::class, 'reservations'])->middleware('role:customer')->name('customer.reservations');
	Route::post('/rezervasyon/{reservation}/iade-talebi', [App\Http\Controllers\CustomerController::class, 'requestRefund'])->name('customer.refunds.store');
});

Route::middleware(['auth', 'role:organizer'])->prefix('yonetim/organizatör')->name('organizer.')->group(function () {
	Route::get('/', [App\Http\Controllers\AdminController::class, 'organizerDashboard'])->name('dashboard');
	Route::post('/ilan', [App\Http\Controllers\AdminController::class, 'storeListing'])->name('listings.store');
	Route::post('/ilan/{listing}', [App\Http\Controllers\AdminController::class, 'updateListing'])->name('listings.update');
	Route::post('/gider', [App\Http\Controllers\AdminController::class, 'storeExpense'])->name('expenses.store');
	Route::post('/iade/{refund}', [App\Http\Controllers\AdminController::class, 'reviewRefund'])->name('refunds.review');
	Route::post('/mesaj/{message}/durum', [App\Http\Controllers\AdminController::class, 'updateMessageStatus'])->name('messages.status');
});

Route::middleware(['auth', 'role:system_admin'])->prefix('yonetim/sistem')->name('system.')->group(function () {
	Route::get('/', [App\Http\Controllers\AdminController::class, 'systemDashboard'])->name('dashboard');
	Route::post('/organizatör', [App\Http\Controllers\AdminController::class, 'createOrganizer'])->name('organizers.store');
	Route::post('/organizatör/{organizer}/yillik-plan', [App\Http\Controllers\AdminController::class, 'createRenewalPlan'])->name('organizers.renewal');
	Route::post('/organizatör/{organizer}/durum', [App\Http\Controllers\AdminController::class, 'toggleOrganizerStatus'])->name('organizers.toggleStatus');
	Route::post('/ücret-taksit/{installment}', [App\Http\Controllers\AdminController::class, 'markInstallmentPaid'])->name('installments.paid');
	Route::post('/ödeme/{payment}/onayla', [App\Http\Controllers\AdminController::class, 'approveTransferPayment'])->name('payments.approve');
	Route::post('/iade/{refund}', [App\Http\Controllers\AdminController::class, 'reviewRefund'])->name('refunds.review');
	Route::post('/kupon', [App\Http\Controllers\AdminController::class, 'storeCoupon'])->name('coupons.store');
	Route::post('/ilan/{listing}/yayina-al', [App\Http\Controllers\AdminController::class, 'publishListing'])->name('listings.publish');
});
