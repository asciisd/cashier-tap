<?php


namespace Asciisd\Cashier\Concerns;


use Asciisd\Cashier\Cashier;
use Tap\Invoice;
use Tap\TapObject;

trait ManagesInvoices
{
    public static function tapInvoices($limit = 25): TapObject|array
    {
        return Invoice::all(['limit' => $limit], Cashier::tapOptions());
    }

    public function createTapInvoice($amount, $trading_account, $currency = 'KWD', $options = [])
    {
        return Invoice::create(array_merge([
            "due" => now()->getPreciseTimestamp(3),
            "expiry" => now()->addDays(10)->getPreciseTimestamp(3),
            "customer" => [
                "id" => $this->tap_id,
            ],
            "order" => [
                "amount" => $amount,
                "currency" => $currency,
                "items" => [
                    [
                        "amount" => $amount,
                        "currency" => $currency,
                        "description" => "Deposit on account #".$trading_account,
                        "name" => "Deposit",
                        "quantity" => 1,
                    ],
                ],
            ],
            "post" => ['url' => url('/tap/webhook', secure: true)],
            "redirect" => ['url' => url(config('cashier.redirect_url'), secure: true)],

        ], $options), Cashier::tapOptions());
    }
}
