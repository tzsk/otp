<?php

namespace Tzsk\Otp\Tests;

use Tzsk\Otp\Facades\Otp;

class OtpTest extends TestCase
{
    public function test_it_has_digits_expiry()
    {
        $manager = new MockOtp();

        $this->assertTrue($manager->getExpiry() > 0);
        $this->assertTrue($manager->getDigits() > 0);
    }

    public function test_digits_can_be_changed()
    {
        $manager = new MockOtp();
        $manager->digits(6);

        $another = (new MockOtp())->digits(8);

        $this->assertEquals(6, $manager->getDigits());
        $this->assertEquals(8, $another->getDigits());
    }

    public function test_expiry_can_be_changed()
    {
        $manager = new MockOtp();
        $manager->expiry(5);

        $another = (new MockOtp())->expiry(6);

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

        $this->assertNotEmpty(otp()->make('baz'));
    }

    public function test_it_will_generate_different_otp_each_time()
    {
        $manager = new MockOtp();
        $this->assertNotEquals($manager->make('foo'), $manager->make('foo'));

        $this->assertNotEquals(Otp::make('bar'), Otp::make('bar'));
        $this->assertNotEquals(otp()->make('bar'), otp()->make('bar'));
    }

    public function test_it_generates_the_same_number_of_digits()
    {
        $manager = (new MockOtp())->digits(8);

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

        // I can validate multiple times
        $this->assertTrue($manager->check($otp, 'bar'));
        $this->assertTrue($manager->check($otp, 'bar'));
    }

    public function test_it_can_forget_the_otp()
    {
        $manager = new MockOtp();
        $otp = $manager->generate('bar');

        $this->assertFalse($manager->check($otp, 'foo'));
        $this->assertTrue($manager->check($otp, 'bar'));

        $manager->forget('bar');
        $this->assertFalse($manager->check($otp, 'bar'));
    }

    public function test_otp_will_be_invalid_after_the_expiry()
    {
        $manager = new MockOtp();
        $otp = $manager->generate('foo');
        $manager->setTime(time() + ($manager->getExpiry() * 100));

        $this->assertFalse($manager->check($otp, 'foo'));
    }
}
