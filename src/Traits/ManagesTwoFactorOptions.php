<?php


namespace Asciisd\Cashier\Traits;


trait ManagesTwoFactorOptions
{
    /**
     * Indicates if two-factor authentication is being offered.
     */
    public static bool $usesTwoFactorAuth = false;

    /**
     * Determines if two-factor authentication is being offered.
     */
    public static function usesTwoFactorAuth(): bool
    {
        return static::$usesTwoFactorAuth;
    }

    /**
     * Specifies that two-factor authentication should be offered.
     */
    public static function useTwoFactorAuth(): void
    {
        static::$usesTwoFactorAuth = true;
    }
}
