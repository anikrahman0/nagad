<?php

use Illuminate\Support\Facades\Route;
use Codeboxr\Nagad\Controllers\NagadPaymentController;

Route::get("/nagad/callback", [NagadPaymentController::class, "callback"]);
Route::post('/create-payment', [NagadPaymentController::class, 'createPayment'])->name('create-payment');
Route::get("/nagad-payment/success", [NagadPaymentController::class, "success"]);
Route::get("/nagad-payment/fail", [NagadPaymentController::class, "fail"]);
Route::get("/nagad-payment/aborted", [NagadPaymentController::class, "aborted"]);
