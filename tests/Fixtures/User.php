<?php

namespace Asciisd\Cashier\Tests\Fixtures;

use Illuminate\Foundation\Auth\User as Model;
use Illuminate\Notifications\Notifiable;
use Asciisd\Cashier\Billable;

class User extends Model
{
    use Billable, Notifiable;
}
