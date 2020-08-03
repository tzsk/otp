<?php

namespace Tzsk\Otp\Contracts;

interface KeyStorable
{
    public function put($key, $value, $ttl = null);

    public function get($key, $default = null);

    public function forget($key);
}