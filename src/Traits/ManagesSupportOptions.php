<?php


namespace Asciisd\Cashier\Traits;


trait ManagesSupportOptions
{
    /**
     * The e-mail address where customer support e-mails should be sent.
     *
     * @var string
     */
    public static string $sendsSupportEmailsTo;

    /**
     * Determine if a support address has been configured.
     *
     * @return bool
     */
    public static function hasSupportAddress(): bool
    {
        return !is_null(static::$sendsSupportEmailsTo);
    }

    /**
     * Get the e-mail address to send customer support e-mails to.
     *
     * @return string
     */
    public static function supportAddress(): string
    {
        return static::$sendsSupportEmailsTo;
    }

    /**
     * Set the e-mail address to send customer support e-mails to.
     *
     * @param string $address
     * @return void
     */
    public static function sendSupportEmailsTo(string $address): void
    {
        static::$sendsSupportEmailsTo = $address;
    }
}
