<?php


namespace Asciisd\Cashier\Traits;


trait ManagesSupportOptions
{
    /**
     * The e-mail address where customer support e-mails should be sent.
     */
    public static string $sendsSupportEmailsTo;

    /**
     * Determine if a support address has been configured.
     */
    public static function hasSupportAddress(): bool
    {
        return !is_null(static::$sendsSupportEmailsTo);
    }

    /**
     * Get the e-mail address to send customer support e-mails to.
     */
    public static function supportAddress(): string
    {
        return static::$sendsSupportEmailsTo;
    }

    /**
     * Set the e-mail address to send customer support e-mails to.
     */
    public static function sendSupportEmailsTo(string $address): void
    {
        static::$sendsSupportEmailsTo = $address;
    }
}
