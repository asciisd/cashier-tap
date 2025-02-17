<?php

namespace Asciisd\Cashier\Http\Controllers;

use Asciisd\Cashier\Cashier;
use Asciisd\Cashier\Events\TapReceiptSeen;
use Asciisd\Cashier\Http\Requests\ReceiptRequest;
use Asciisd\Cashier\Payment;
use Tap\Charge;

class ReceiptController
{
    /**
     * Display receipt.
     */
    public function show(ReceiptRequest $request)
    {
        $payment = new Payment(
            Charge::retrieve($request->tap_id, Cashier::tapOptions())
        );

        TapReceiptSeen::dispatch($payment);

        return view('cashier::receipt', ['payment' => $payment] + Cashier::invoiceDataFor($payment->owner()));
    }
}
