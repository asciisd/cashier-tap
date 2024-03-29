<?php

namespace Asciisd\Cashier\Http\Controllers;

use Asciisd\Cashier\Cashier;
use Asciisd\Cashier\Events\TapReceiptSeen;
use Asciisd\Cashier\Http\Requests\ReceiptRequest;
use Asciisd\Cashier\Payment;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\View\View;
use Tap\Charge;

class ReceiptController
{
    /**
     * Display receipt.
     *
     * @param ReceiptRequest $request
     * @return View
     */
    public function show(ReceiptRequest $request)
    {
        $payment = new Payment(
            Charge::retrieve($request->tap_id, Cashier::tapOptions())
        );

//        if ($request->user()->tap_id !== $payment->owner()->tap_id) {
//            throw new UnauthorizedException('Sorry! But this invoice did\'t belongs to you.');
//        }

        TapReceiptSeen::dispatch($payment);

        return view('cashier::receipt', ['payment' => $payment] + Cashier::invoiceDataFor($payment->owner()));
    }
}
