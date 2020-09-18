<?php

namespace Tzsk\Otp\Tests;

use Illuminate\Cache\FileStore;
use Illuminate\Cache\Repository;
use Illuminate\Filesystem\Filesystem;
use Tzsk\Otp\Otp;

class MockOtp extends Otp
{
    public function __construct()
    {
        $directory = './tests/phpunit-cache';
        $store = new Repository(new FileStore(new Filesystem(), $directory));

        parent::__construct($store);
    }

    public function getExpiry()
    {
        return $this->expiry;
    }

    public function getDigits()
    {
        return $this->digits;
    }

    public function setTime($time)
    {
        $this->time = $time;
    }
}
