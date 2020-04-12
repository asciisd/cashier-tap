<?php

use Illuminate\Support\Facades\Route;

Route::get('receipt', 'ReceiptController@show')->name('receipt');
Route::post('webhook', 'WebhookController@handleWebhook')->name('webhook');
