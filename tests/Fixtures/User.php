<?php

namespace Asciisd\Cashier\Tests\Fixtures;

use Asciisd\Cashier\Billable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements \Asciisd\Cashier\Contracts\Billable
{
    use Billable, Notifiable;

    protected $guarded = [];
}
