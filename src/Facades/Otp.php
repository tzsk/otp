<?php

namespace Tzsk\Otp\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Tzsk\Otp\Otp digits(int $digits)
 * @method static \Tzsk\Otp\Otp expiry(int $digits)
 * @method static string make(string $key)
 * @method static string create(string $key)
 * @method static string generate(string $key)
 * @method static boolean match(mixed $otp, string $key)
 * @method static boolean check(mixed $otp, string $key)
 * @method static boolean verify(mixed $otp, string $key)
 *
 * @see \Tzsk\Otp\Otp
 */
class Otp extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'tzsk-otp';
    }
}
