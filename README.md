[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-downloads]
[![Total Downloads][ico-stars]]()

# Introduction

Laravel Cashier-tap provides an expressive, fluent interface to Tap's company subscription billing services. It handles
almost all of the boilerplate subscription billing code you are dreading writing. In addition to basic subscription
management, Cashier can handle coupons, swapping subscription, subscription "quantities", cancellation grace periods,
and even generate invoice PDFs.

## Installation

#### 1- Run composer required command:

You can install the bindings via [Composer][link-composer]. Run the following command:

```bash
composer require asciisd/cashier-tap
```

#### 2- Run install command:

this command will install `ServiceProvider`, `Configs` and `views`

```bash
php artisan cashier:install
```

#### 3- Run publish command:

this command will knet assets

```bash
php artisan cashier:publish
```

#### 4- Run migration command:

table by run the migrations:

```bash
php artisan migrate
```

#### 5- Add `.env` parameters

```dotenv
TAP_API_KEY=
TAP_WEBHOOK_SECRET= //use this key if you want tap to post the result back to your application
```

## Using this package

`not yet finished, you can use this package as following example:-`

add `Billable` trait to the User model

```php
namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Asciisd\Cashier\Billable;

class User extends Authenticatable {
   use Billable;
}
```

`use pay() method`

```php
try{
    //allowed payment methods is ['src_kw.knet', 'src_all', 'src_card']
    $payment_method = 'src_card';
    $payment = $user->charge(10, $payment_method);

    $payment->url; // this will return payment link
} catch(\Asciisd\Cashier\Exceptions\PaymentActionRequired $exception) {
    $payment = $exception->payment;
}

return $payment->actionUrl();
```

> After finished the payment you will redirect to [/tap/receipt]()
you can change that from config file to make your own handler, so please make sure to add this directory to the VerifyCsrfToken $except
> also if you want to use webhook you should add [tap/webhook]() also to the VerifyCsrfToken $except method

## Test cards

| Card Number | Expiry Date | PIN | Status |
| ---------------- | :----- | :---- | :------------ |
| 5111111111111118 | 05/21 | 100 | CAPTURED |
| 8888880000000002 | 05/22 | 100 | NOT CAPTURED |

## Contributing

Thank you for considering contributing to Cashier!

## License

Laravel Cashier-tap is open-sourced software licensed under the [MIT license](LICENSE.md).

[ico-version]: https://img.shields.io/packagist/v/asciisd/cashier-tap.svg?style=flat

[ico-license]: https://img.shields.io/github/license/asciisd/cashier-tap?style=flat

[ico-downloads]: https://img.shields.io/packagist/dt/asciisd/cashier-tap.svg?style=flat

[ico-issues]: https://img.shields.io/github/issues/asciisd/cashier-tap?style=flat

[ico-fork]: https://img.shields.io/github/forks/asciisd/cashier-tap?style=flat

[ico-stars]: https://img.shields.io/github/stars/asciisd/cashier-tap?style=flat

[link-packagist]: https://packagist.org/packages/asciisd/cashier-tap

[link-downloads]: https://packagist.org/packages/asciisd/cashier-tap

[link-author]: https://github.com/aemaddin

[link-contributors]: ../../contributors

[link-tap]: https://tap.company


[link-composer]: http://getcomposer.org/
