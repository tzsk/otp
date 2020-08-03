<?php

use Tzsk\Otp\CacheKeyStore;
use Tzsk\Otp\FileKeyStore;
use Tzsk\Otp\OtpManager;

if (! function_exists('otp')) {
    /**
     * @param string|null $directory
     * @return OtpManager
     */
    function otp($directory = null)
    {
        return $directory ?
            new OtpManager(new FileKeyStore($directory)) :
            (new OtpManager(new CacheKeyStore()))
                ->digits(config('otp.digits'))
                ->expiry(config('otp.expiry'));
    }
}
