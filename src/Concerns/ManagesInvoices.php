<?php


namespace Asciisd\Cashier\Concerns;


use Asciisd\Cashier\Cashier;
use Tap\Invoice;

trait ManagesInvoices
{
    public static function tapInvoices()
    {
        Invoice::all(['limit' => 25], Cashier::tapOptions());
    }
}
