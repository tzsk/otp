<?php

namespace Tzsk\Otp;

use Closure;
use DateInterval;
use Exception;
use Illuminate\Contracts\Cache\Repository;

/**
 * @method string make(string $key)
 * @method string create(string $key)
 * @method bool match(mixed $otp, string $key)
 * @method bool check(mixed $otp, string $key)
 */
class Otp
{
    protected Repository $store;

    protected int $expiry = 600;

    protected int $digits = 4;

    public function __construct(Repository $store)
    {
        $this->store = $store;
    }

    protected function getFreshTime(): int
    {
        return time();
    }

    public function expiry($expiry): self
    {
        $seconds = (int) $expiry * 60;

        if ($seconds > 0) {
            $this->expiry = $seconds;
        }

        return $this;
    }

    public function digits($digits): self
    {
        $intDigits = (int) $digits;

        if ($intDigits > 0) {
            $this->digits = $intDigits;
        }

        return $this;
    }

    public function generate($key): string
    {
        $secret = sha1(uniqid());
        $expiry = $this->expiry;
        $ttl = DateInterval::createFromDateString("{$expiry} seconds");
        $this->store->put($this->keyFor($key), $secret, $ttl);

        return $this->calculate($secret);
    }

    public function check($code, $key): bool
    {
        $secret = $this->store->get($this->keyFor($key));
        if (empty($secret)) {
            return false;
        }

        if ($code == $this->calculate($secret)) {
            return true;
        }

        $factor = ($this->getFreshTime() - floor($this->expiry / 2)) / $this->expiry;

        return $code == $this->calculate($secret, $factor);
    }

    public function forget($key): bool
    {
        return $this->store->forget($this->keyFor($key));
    }

    protected function keyFor($key): string
    {
        return md5(sprintf('%s-%s', 'tzsk-otp', $key));
    }

    protected function calculate($secret, $factor = null): string
    {
        $hash = hash_hmac('sha1', $this->timeFactor($factor), $secret, true);
        $offset = ord($hash[strlen($hash) - 1]) & 0xF;

        $hash = str_split($hash);
        foreach ($hash as $index => $value) {
            $hash[$index] = ord($value);
        }

        $binary = (($hash[$offset] & 0x7F) << 24) | (($hash[$offset + 1] & 0xFF) << 16) | (($hash[$offset + 2] & 0xFF) << 8) | ($hash[$offset + 3] & 0xFF);

        $otp = $binary % pow(10, $this->digits);

        return str_pad((string) $otp, $this->digits, '0', STR_PAD_LEFT);
    }

    protected function timeFactor($divisionFactor): string
    {
        $factor = $divisionFactor ? floor($divisionFactor) : floor($this->getFreshTime() / $this->expiry);

        $text = [];
        for ($i = 7; $i >= 0; $i--) {
            $text[] = ($factor & 0xFF);
            $factor >>= 8;
        }
        $text = array_reverse($text);
        foreach ($text as $index => $value) {
            $text[$index] = chr((int) $value);
        }

        return  implode('', $text);
    }

    protected function alias($key): ?Closure
    {
        $aliases = [
            'make' => fn (array $args) => $this->generate(...$args),
            'create' => fn (array $args) => $this->generate(...$args),
            'verify' => fn (array $args) => $this->check(...$args),
            'match' => fn (array $args) => $this->check(...$args),
        ];

        return data_get($aliases, $key);
    }

    public function __call($method, $args)
    {
        $alias = $this->alias($method);
        if ($alias) {
            return call_user_func($alias, $args);
        }

        throw new Exception('Method does not exist');
    }
}
