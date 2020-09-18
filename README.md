# :gift: OTP Generator & Verifier

![OTP](resources/otp.svg)

![GitHub License](https://img.shields.io/github/license/tzsk/otp?style=for-the-badge)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/tzsk/otp.svg?style=for-the-badge&logo=composer)](https://packagist.org/packages/tzsk/otp)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/tzsk/otp/Tests?label=tests&style=for-the-badge&logo=github)](https://github.com/tzsk/otp/actions?query=workflow%3ATests+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/tzsk/otp.svg?style=for-the-badge&logo=laravel)](https://packagist.org/packages/tzsk/otp)


This is a tool to create OTP with an expiry for PHP without using any Database. This is primarily a Laravel Package but it can be used outside of Laravel also.

## :package: Installation

Via Composer

```bash
composer require tzsk/otp
```

To publish the config file for laravel you can run

```bash
php artisan otp:publish
```

## :fire: Usage in Laravel

Import the facade class:
```php
use Tzsk\Otp\Facades\Otp;
```

**Generate an OTP:**

```php
$otp = Otp::generate($unique_secret);
// Returns - string
```

The above generated OTP will only be validated using the same unique secret within the default expiry time.

> **TIP:** OTP is generally used for user verification. So the easiest way of determining the `uniqe secret` is the user's email or phone number. Or maybe even the User ID. You can even get creative about the unique secret. You can use `md5($email)` the md5 of user's email or phone number.

**Match an OTP:**

```php
$valid = Otp::match($otp, $unique_secret);
// Returns - boolean
```

**Other Generate & Match Options:**

There are other ways of generating or matching an OTP:

```php
// Generate -

Otp::digits(8)->generate($unique_secret); // 8 Digits, Default expiry from config
Otp::expiry(30)->generate($unique_secret); // 30 min expiry, Default digits from config
Otp::digits(8)->expiry(30)->generate($unique_secret); // 8 digits, 30 min expiry

// The above generate method can be swaped with other generator methods. Ex -
Otp::make($unique_secret);
Otp::create($unique_secret);
```

Make sure to set the same config during checking. What that means is, if you have used 8 digits and 30 min during creation you will also have to use 8 digits and 30 min during checking as well.

```php
// Match - (Different Runtime)

// The first example above
Otp::check($otp, $unique_secret); // -> false
Otp::digits(8)->check($otp, $unique_secret); // -> true

// The second example above
Otp::check($otp, $unique_secret); // -> false
Otp::expiry(30)->check($otp, $unique_secret); // -> true

// The third example above
Otp::check($otp, $unique_secret); // -> false
Otp::digits(8)->expiry(30)->check($otp, $unique_secret); // -> true
```

Here, in the above example for matching the OTP we can see that the same config is required when matching the otp with the secret which was used during creation of the OTP.

**Security Advantage:** - The main advantage of using the same config while matching is some third person cannot use this tool to generate the same otp for the user in question if he doesn't know the config.

### :ocean: Helper usage

You can use the package with provided helper function as well
```php
$otp = otp()->make($secret);
$otp = otp()->digits(8)->expiry(20)->make($secret);
```

## :heart_eyes: Usage outside Laravel

Install the package with composer the same way as above. Then just use it with the helper function provided.
**Generate:**

```php
/**
 * Now you need to have a directory in your filesystem where the package can do it's magic.
 * Make sure you prevent access to this directory and files using apache or ngnix config.
 */

// Let's assume the directory you have created is `./otp-tmp`
$manager = otp('./otp-tmp');

/**
 * Default properties - 
 * $digits -> 4
 * $expiry -> 10 min
 */

$manager->digits(6); // To change the number of OTP digits
$manager->expiry(20); // To change the mins until expiry

$manager->generate($unique_secret); // Will return a string of OTP

$manager->match($otp, $unique_secret); // Will return true or false.
```

All of the functionalities are the same as it is been documented in Laravel Usage section. Here just use the instance instead of the Static Facade.

**NOTE:** You don't need to do anything if you are using Laravel. It will detect the default cache store of laravel.

Example:

```php
$manager->digits(...)->expiry(...)->generate($unique_secret);

// And...

$manager->digits(...)->expiry(...)->match($otp, $unique_secret);
```

Also, keep in mind that while matching the OTP keep the digit & expiry config same as when the OTP was generated.

## :microscope: Testing

``` bash
composer test
```

## :date: Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## :heart: Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## :lock: Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## :crown: Credits

- [Kazi Ahmed](https://github.com/tzsk)
- [All Contributors](../../contributors)

## :policeman: License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
