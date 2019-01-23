<?php

namespace Tzsk\Otp;

class OtpManager
{
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
    protected $aliasgenerate = ['make', 'create'];

    /**
     * Alias methods for check.
     *
     * @var array
     */
    protected $aliasCheck = ['verify', 'match'];

    /**
     * OtpFactory Constructor.
     *
     * @param int $digits
     * @param int $expiry
     */
    public function __construct($digits = null, $expiry = null)
    {
        $this->time = time();
        if ($digits) {
            $this->digits($digits);
        }

        if ($expiry) {
            $this->expiry($expiry);
        }
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
     * @param string $secret
     * @return string
     */
    public function generate($secret)
    {
        return $this->calculate($secret);
    }

    /**
     * @param string $code
     * @param string $secret
     * @return bool
     */
    public function check($code, $secret)
    {
        if ($code == $this->calculate($secret)) {
            return true;
        }

        $factor = ($this->time - floor($this->expiry / 2)) / $this->expiry;

        return $code == $this->calculate($secret, $factor);
    }

    /**
     * @param string $secret
     * @param float $factor
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
     */
    public function __call($method, $args)
    {
        if (in_array($method, $this->aliasgenerate)) {
            return $this->generate($args[0]);
        }

        if (in_array($method, $this->aliasCheck)) {
            return $this->check($args[0], $args[1]);
        }

        throw new \Exception('Method does not exist');
    }
}
