<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web']], function () {
    Route::get('receipt', 'ReceiptController@show')
        ->middleware('auth')
        ->name('receipt');
});
Route::post('webhook', 'WebhookController@handleWebhook')->name('webhook');
