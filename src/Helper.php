<?php

use Illuminate\Cache\FileStore;
use Illuminate\Cache\Repository;
use Illuminate\Filesystem\Filesystem;
use Tzsk\Otp\Otp;

if (! function_exists('otp')) {

    /**
     * @param string|null $directory
     * @return Otp
     */
    function otp(string $directory = null): Otp
    {
        if ($directory) {
            $store = new Repository(new FileStore(new Filesystem(), $directory));

            return new Otp($store);
        }

        return app('tzsk-otp');
    }
}
