<?php

use Tzsk\Otp\Facades\Otp;
use Tzsk\Otp\Tests\MockOtp;

it('has digits and expiry', function () {
    $manager = new MockOtp;

    expect($manager->getExpiry())->toBeGreaterThan(0)
        ->and($manager->getDigits())->toBeGreaterThan(0);
});

it('can change digits', function () {
    $manager = new MockOtp;
    $manager->digits(6);

    $another = (new MockOtp)->digits(8);

    expect($manager->getDigits())->toBe(6)
        ->and($another->getDigits())->toBe(8);
});

it('can change expiry', function () {
    $manager = new MockOtp;
    $manager->expiry(5);

    $another = (new MockOtp)->expiry(6);

    expect($manager->getExpiry())->toBe(300)
        ->and($another->getExpiry())->toBe(360);
});

it('can generate otp', function () {
    $manager = new MockOtp;

    expect($manager->generate('foo'))->not->toBeEmpty()
        ->and($manager->make('foo'))->not->toBeEmpty()
        ->and($manager->create('foo'))->not->toBeEmpty();

    $otp = Otp::generate('bar');
    expect($otp)->not->toBeEmpty();

    expect(otp()->make('baz'))->not->toBeEmpty();
});

it('will generate different otp each time', function () {
    $manager = new MockOtp;
    expect($manager->make('foo'))->not->toBe($manager->make('foo'));

    expect(Otp::make('bar'))->not->toBe(Otp::make('bar'));
    expect(otp()->make('bar'))->not->toBe(otp()->make('bar'));
});

it('generates the same number of digits', function () {
    $manager = (new MockOtp)->digits(8);

    expect(strlen((string) $manager->generate('foo')))->toBe(8);

    $manager->digits(6);
    expect(strlen((string) $manager->generate('foo')))->toBe(6);
});

it('validates the otp', function () {
    $manager = new MockOtp;
    $otp = $manager->generate('bar');

    expect($manager->check($otp, 'foo'))->toBeFalse()
        ->and($manager->check($otp, 'bar'))->toBeTrue();

    // A validated OTP is single-use and cannot be replayed.
    expect($manager->check($otp, 'bar'))->toBeFalse()
        ->and($manager->check($otp, 'bar'))->toBeFalse();
});

it('accepts the code as string or integer but rejects type juggling', function () {
    $manager = new MockOtp;

    // The genuine code validates as its exact string, then as an integer.
    // A fresh code is used for each, since a validated OTP is now single-use.
    $otp = $manager->generate('bar');
    expect($manager->check($otp, 'bar'))->toBeTrue();

    $otp = $manager->generate('bar');
    expect($manager->check((int) $otp, 'bar'))->toBeTrue();

    // With a live code present, non-string / non-int inputs cannot masquerade as valid.
    $manager->generate('bar');
    expect($manager->check(true, 'bar'))->toBeFalse()
        ->and($manager->check(false, 'bar'))->toBeFalse()
        ->and($manager->check(null, 'bar'))->toBeFalse()
        ->and($manager->check([], 'bar'))->toBeFalse()
        ->and($manager->check('', 'bar'))->toBeFalse();
});

it('can forget the otp', function () {
    $manager = new MockOtp;
    $otp = $manager->generate('bar');

    expect($manager->check($otp, 'foo'))->toBeFalse()
        ->and($manager->check($otp, 'bar'))->toBeTrue();

    $manager->forget('bar');
    expect($manager->check($otp, 'bar'))->toBeFalse();
});

it('will be invalid after the expiry', function () {
    $manager = new MockOtp;
    $otp = $manager->generate('foo');
    $manager->setTestTime(time() + ($manager->getExpiry() * 100));

    expect($manager->check($otp, 'foo'))->toBeFalse();
});
