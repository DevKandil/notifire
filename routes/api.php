<?php

use DevKandil\NotiFire\Http\Controllers\FcmController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('fcm/token', [FcmController::class, 'updateToken'])->name('fcm.update-token');
});