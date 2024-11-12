<?php

namespace Asciisd\Cashier\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\Model;

class InvalidCustomer extends Exception
{
    /**
     * Create a new InvalidTapCustomer instance.
     */
    public static function nonCustomer(Model $owner): static
    {
        return new static(class_basename($owner).' is not a Tap customer. See the createAsTapCustomer method.');
    }

    /**
     * Create a new InvalidTapCustomer instance.
     */
    public static function exists(Model $owner): static
    {
        return new static(class_basename($owner)." is already a Tap customer with ID {$owner->tap_id}.");
    }
}
