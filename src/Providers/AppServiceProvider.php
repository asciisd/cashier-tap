<?php

namespace Asciisd\Cashier\Providers;

use Asciisd\Cashier\Cashier;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Your application and company details.
     *
     * @var array
     */
    protected $details = [];

    /**
     * All of the application developer e-mail addresses.
     *
     * @var array
     */
    protected $developers = [];

    /**
     * The address where customer support e-mails should be sent.
     *
     * @var string
     */
    protected $sendSupportEmailsTo = null;
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Cashier::details($this->details);
        Cashier::sendSupportEmailsTo($this->sendSupportEmailsTo);

        if (count($this->developers) > 0) {
            Cashier::developers($this->developers);
        }

        $this->booted();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
