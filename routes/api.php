<?php

use App\Http\Controllers\Api\ServiceApiController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api.token'])->prefix('v1')->name('api.v1.')->group(function () {

    Route::get('services',      [ServiceApiController::class, 'index'])->name('services');
    Route::get('balance',       [ServiceApiController::class, 'balance'])->name('balance');
    Route::get('orders',        [ServiceApiController::class, 'orders'])->name('orders');
    Route::get('order-status',  [ServiceApiController::class, 'orderStatus'])->name('order-status');
    Route::post('add-order',    [ServiceApiController::class, 'addOrder'])->name('add-order');
    Route::post('cancel-order', [ServiceApiController::class, 'cancelOrder'])->name('cancel-order');
    Route::post('refill-order', [ServiceApiController::class, 'refillOrder'])->name('refill-order');

});
