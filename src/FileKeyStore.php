<?php

namespace Tzsk\Otp;

use DateInterval;
use Illuminate\Cache\FileStore;
use Illuminate\Cache\Repository;
use Illuminate\Filesystem\Filesystem;
use Tzsk\Otp\Contracts\KeyStorable;

class FileKeyStore implements KeyStorable
{
    /**
     * @var Repository
     */
    protected $store;

    /**
     * FileKeyStore constructor.
     *
     * @param string $directory
     */
    public function __construct($directory)
    {
        $this->store = new Repository(new FileStore(new Filesystem(), $directory));
    }

    /**
     * @param string $key
     * @param string $value
     * @param DateInterval|null $ttl
     * @return bool
     */
    public function put($key, $value, $ttl = null)
    {
        return $this->store->put($key, $value, $ttl);
    }

    /**
     * @param string $key
     * @param string|null $default
     * @return array|mixed
     */
    public function get($key, $default = null)
    {
        return $this->store->get($key, $default);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function forget($key)
    {
        return $this->store->forget($key);
    }
}
