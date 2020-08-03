# Laravel OTP

[![Software License][ico-license]][link-license]
[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-travis]][link-travis]
[![StyleCI][ico-styleci]][link-styleci]
[![Quality Score][ico-quality]][link-quality]

This is tool to create OTP with an expiry for PHP without using any Database. This is primaryly a Laravel Package but it can be used outside of Laravel also.

## Installation

Via Composer

``` bash
$ composer require tzsk/otp
```

## Configuration

Add the Provider & Alias in the `config/app.php` file.

```php
'providers' => [
    //...
    Tzsk\Otp\Providers\OtpServiceProvider::class,
]

'aliases' => [
    //...
    'Otp' => Tzsk\Otp\Facades\Otp::class,
]
```

> If you are using `Laravel 5.5` or higher you don't need to do above mentioned steps you can directly do the following.

Now, just publish the config file by running this command.

```bash
$ php artisan vendor:publish --tag=tzsk-otp
```

## Usage in Laravel

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

Make sure to set the same config during checking. What that means is, if you have used 8 digits and 30 min during creation you will also have to use 8 digits and 30 min during checking as well. It will match in same runtime if you don't set the same config but if you want to check later in a different runtime then it will fail.

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

### Helper usage

You can use the package with provided helper function as well
```php
$otp = otp()->make($secret);
$otp = otp()->digits(8)->expiry(20)->make($secret);
```

## Usage outside Laravel.

Install the package with composer the same way as above. Then just new up the `OtpManager` class.

**Generate:**

```php
// Use the calss path.
use Tzsk\Otp\OtpManager;
use Tzsk\Otp\FileKeyStore;

/**
 * Now you need to have a directory in your filesystem where the package can do it's magic
 * Create the FileKeyStore instance with the directory you've created for this
 */

// Let's assume the directory you have created is `./otp-tmp`

$directory = './otp-tmp';
$store = new FileKeyStore($directory);
$manager = new OtpManager($store);

// You can even inline all the above step
$manager = new OtpManager(new FileKeyStore('./otp-tmp'));

// If you want you can use the otp helper as well
$manager = otp('./otp-tmp');

/**
 * Required parameter of type Tzks\Otp\Contracts\KeyStorable
 * 
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

## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email author email instead of using the issue tracker.

## Credits

- [Kazi Mainuddin Ahmed][link-author]
- [All Contributors][link-contributors]

## License

license. Please see the [license file](license.md) for more information.

[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-version]: https://img.shields.io/packagist/v/tzsk/otp.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/tzsk/otp.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/tzsk/otp/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/167214907/shield
[ico-quality]: https://img.shields.io/scrutinizer/g/tzsk/otp.svg?style=flat-square

[link-license]: license.md
[link-packagist]: https://packagist.org/packages/tzsk/otp
[link-downloads]: https://packagist.org/packages/tzsk/otp
[link-travis]: https://travis-ci.org/tzsk/otp
[link-styleci]: https://styleci.io/repos/167214907
[link-quality]: https://scrutinizer-ci.com/g/tzsk/otp

[link-author]: https://github.com/tzsk
[link-contributors]: ../../contributors