<?php

namespace Tzsk\Otp\Facades;

use Illuminate\Support\Facades\Facade;
use Tzsk\Otp\OtpManager;

/**
 * Class Otp
 * @package Tzsk\Otp\Facades
 *
 * @method static OtpManager digits(int $digits)
 * @method static OtpManager expiry(string $key)
 * @method static string generate(string $key)
 * @method static string make(string $key)
 * @method static string create(string $key)
 * @method static bool check(string $otp, string $key)
 * @method static bool verify(string $otp, string $key)
 * @method static bool match(string $otp, string $key)
 */
class Otp extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'tzsk-otp';
    }
}
