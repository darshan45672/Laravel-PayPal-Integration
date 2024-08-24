<?php

use App\Http\Controllers\PayPalController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

Route::post('paypal', [PayPalController::class, 'createPayment'])->name('paypal');
Route::post('success', [PayPalController::class, 'success'])->name('success');
Route::post('cancel', [PayPalController::class, 'cancel'])->name('cancel');