<?php

namespace Tzsk\Otp;

use DateInterval;
use Illuminate\Support\Facades\Cache;
use Tzsk\Otp\Contracts\KeyStorable;

class CacheKeyStore implements KeyStorable
{
    /**
     * @param string $key
     * @param string $value
     * @param DateInterval|null $ttl
     * @return bool
     */
    public function put($key, $value, $ttl = null)
    {
        return Cache::put($key, $value, $ttl);
    }

    /**
     * @param string $key
     * @param string|null $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return Cache::get($key, $default);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function forget($key)
    {
        return Cache::forget($key);
    }
}
