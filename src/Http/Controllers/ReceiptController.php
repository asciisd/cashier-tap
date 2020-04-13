<?php

namespace Asciisd\Cashier\Http\Controllers;

use Asciisd\Cashier\Cashier;
use Asciisd\Cashier\Events\TapReceiptSeen;
use Asciisd\Cashier\Payment;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Tap\Charge;

class ReceiptController
{
    /**
     * Display receipt.
     *
     * @param Request $request
     * @return View
     */
    public function show(Request $request)
    {
        $payment = new Payment(
            Charge::retrieve($request->tap_id, Cashier::tapOptions())
        );

        TapReceiptSeen::dispatch($payment);

        return view('cashier::receipt', ['payment' => $payment] + Cashier::invoiceDataFor($payment->owner()));
    }
}
