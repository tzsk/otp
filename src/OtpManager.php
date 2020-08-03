<?php

namespace Tzsk\Otp;

use DateInterval;
use Exception;
use Tzsk\Otp\Contracts\KeyStorable;

/**
 * Class OtpManager.
 *
 * @method string make(string $key)
 * @method string create(string $key)
 * @method bool match(string $otp, string $key)
 * @method bool verify(string $otp, string $key)
 */
class OtpManager
{
    /**
     * @var KeyStorable
     */
    protected $store;

    /**
     * Otp expiry limit - Default: 10 min.
     *
     * @var int
     */
    protected $expiry = 600;

    /**
     * Otp digits - Default: 4.
     *
     * @var int
     */
    protected $digits = 4;

    /**
     * @var int
     */
    protected $time;

    /**
     * Alias methods for generate.
     *
     * @var array
     */
    protected $aliasGenerate = ['make', 'create'];

    /**
     * Alias methods for check.
     *
     * @var array
     */
    protected $aliasCheck = ['verify', 'match'];

    /**
     * OtpFactory Constructor.
     *
     * @param KeyStorable $store
     */
    public function __construct(KeyStorable $store)
    {
        $this->time = time();
        $this->store = $store;
    }

    /**
     * @param int $expiry
     * @return self
     */
    public function expiry($expiry)
    {
        $seconds = (int) $expiry * 60;

        if ($seconds > 0) {
            $this->expiry = $seconds;
        }

        return $this;
    }

    /**
     * @param int $digits
     * @return self
     */
    public function digits($digits)
    {
        $intDigits = (int) $digits;

        if ($intDigits > 0) {
            $this->digits = $intDigits;
        }

        return $this;
    }

    /**
     * @param string $key
     * @return string
     */
    public function generate($key)
    {
        $secret = sha1(uniqid());
        $ttl = DateInterval::createFromDateString("{$this->time} seconds");
        $this->store->put($this->keyFor($key), $secret, $ttl);

        return $this->calculate($secret);
    }

    /**
     * @param string $code
     * @param string $key
     * @return bool
     */
    public function check($code, $key)
    {
        $secret = $this->store->get($this->keyFor($key));
        if (empty($secret)) {
            return false;
        }

        if ($code == $this->calculate($secret)) {
            return true;
        }

        $factor = ($this->time - floor($this->expiry / 2)) / $this->expiry;

        return $code == $this->calculate($secret, $factor);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function forget($key)
    {
        return $this->store->forget($this->keyFor($key));
    }

    /**
     * @param string $key
     * @return string
     */
    protected function keyFor($key)
    {
        return md5(sprintf('%s-%s', 'tzsk-otp', $key));
    }

    /**
     * @param string $secret
     * @param float|null $factor
     * @return string
     */
    protected function calculate($secret, $factor = null)
    {
        $hash = hash_hmac('sha1', $this->timeFactor($factor), $secret, true);
        $hashLenght = strlen($hash);
        $offset = ord($hash[$hashLenght - 1]) & 0xf;

        $hash = str_split($hash);
        foreach ($hash as $index => $value) {
            $hash[$index] = ord($value);
        }

        $binary = (($hash[$offset] & 0x7f) << 24) | (($hash[$offset + 1] & 0xff) << 16) | (($hash[$offset + 2] & 0xff) << 8) | ($hash[$offset + 3] & 0xff);

        $otp = $binary % pow(10, $this->digits);

        return str_pad($otp, $this->digits, '0', STR_PAD_LEFT);
    }

    /**
     * @param float|null $divisionFactor
     * @return string
     */
    protected function timeFactor($divisionFactor)
    {
        $factor = $divisionFactor ? floor($divisionFactor) : floor($this->time / $this->expiry);

        $text = [];
        for ($i = 7; $i >= 0; $i--) {
            $text[] = ($factor & 0xff);
            $factor >>= 8;
        }
        $text = array_reverse($text);
        foreach ($text as $index => $value) {
            $text[$index] = chr($value);
        }

        return  implode('', $text);
    }

    /**
     * @param string $method
     * @param array $args
     * @return mixed
     * @throws Exception
     */
    public function __call($method, $args)
    {
        if (in_array($method, $this->aliasGenerate)) {
            return $this->generate($args[0]);
        }

        if (in_array($method, $this->aliasCheck)) {
            return $this->check($args[0], $args[1]);
        }

        throw new Exception('Method does not exist');
    }
}
