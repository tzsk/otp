<?php

use Illuminate\Cache\FileStore;
use Illuminate\Cache\Repository;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Cache;
use Tzsk\Otp\Otp;

if (! function_exists('otp')) {

    /**
     * @param string $directory
     * @return Otp
     */
    function otp($directory = null)
    {
        if ($directory) {
            $store = new Repository(new FileStore(new Filesystem(), $directory));

            return new Otp($store);
        }

        ['digits' => $digits, 'expiry' => $expiry] = config('otp');

        return (new Otp(Cache::store()))->digits($digits)->expiry($expiry);
    }
}
