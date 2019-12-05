<?php

namespace Tzsk\Otp\Tests;

use Tzsk\Otp\Facades\Otp;
use Tzsk\Otp\OtpManager;

class OtpManagerTest extends TestCase
{
    public function test_it_has_digits_expiry()
    {
        $manager = new MockOtp();

        $this->assertTrue($manager->getExpiry() > 0);
        $this->assertTrue($manager->getDigits() > 0);
    }

    public function test_digits_can_be_canged()
    {
        $manager = new MockOtp();
        $manager->digits(6);

        $another = new MockOtp(8);

        $this->assertEquals(6, $manager->getDigits());
        $this->assertEquals(8, $another->getDigits());
    }

    public function test_expiry_can_be_canged()
    {
        $manager = new MockOtp();
        $manager->expiry(5);

        $another = new MockOtp(null, 6);

        $this->assertEquals(300, $manager->getExpiry());
        $this->assertEquals(360, $another->getExpiry());
    }

    public function test_it_can_generate_otp()
    {
        $manager = new MockOtp();

        $this->assertNotEmpty($manager->generate('foo'));
        $this->assertNotEmpty($manager->make('foo'));
        $this->assertNotEmpty($manager->create('foo'));

        $otp = Otp::generate('bar');
        $this->assertNotEmpty($otp);
    }

    public function test_it_generates_the_same_number_of_digits()
    {
        $manager = new MockOtp(8);

        $this->assertEquals(8, strlen($manager->generate('foo')));

        $manager->digits(6);
        $this->assertEquals(6, strlen($manager->generate('foo')));
    }

    public function test_it_validates_the_otp()
    {
        $manager = new MockOtp();
        $otp = $manager->generate('bar');

        $this->assertFalse($manager->check($otp, 'foo'));
        $this->assertTrue($manager->check($otp, 'bar'));
    }

    public function test_otp_will_be_invalid_after_the_expiry()
    {
        $manager = new MockOtp();
        $otp = $manager->generate('foo');
        $manager->setTime(time() + ($manager->getExpiry() * 100));

        $this->assertFalse($manager->check($otp, 'foo'));
    }
}

class MockOtp extends OtpManager
{
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
