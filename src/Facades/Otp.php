<?php

namespace Tzsk\Otp\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Tzsk\Otp\Otp
 */
class Otp extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'tzsk-otp';
    }
}
