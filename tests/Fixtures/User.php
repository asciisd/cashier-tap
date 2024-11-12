<?php

namespace Asciisd\Cashier\Tests\Fixtures;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Asciisd\Cashier\Billable;

class User extends Authenticatable implements \Asciisd\Cashier\Contracts\Billable
{
    use Billable, Notifiable;

    protected $guarded = [];
}
