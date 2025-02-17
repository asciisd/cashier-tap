<?php

namespace Asciisd\Cashier\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\Model;

class CustomerAlreadyCreated extends Exception
{
    /**
     * Create a new CustomerAlreadyCreated instance.
     *
     * @param Model $owner
     * @return static
     */
    public static function exists($owner)
    {
        return new static(class_basename($owner)." is already a Tap customer with ID {$owner->tap_id}.");
    }
}

