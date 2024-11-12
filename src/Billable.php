<?php


namespace Asciisd\Cashier;

use Asciisd\Cashier\Concerns\ManagesCustomer;
use Asciisd\Cashier\Concerns\ManagesInvoices;
use Asciisd\Cashier\Concerns\ManagesPaymentMethods;
use Asciisd\Cashier\Concerns\ManagesSubscriptions;
use Asciisd\Cashier\Concerns\PerformsCharges;

trait Billable
{
    use ManagesCustomer;
    use ManagesInvoices;
    use ManagesPaymentMethods;
    use ManagesSubscriptions;
    use PerformsCharges;
}
