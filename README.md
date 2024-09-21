# OTPz (OT-Peasy): Secure One-Time Passwords For Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/benbjurstrom/otpz.svg?style=flat-square)](https://packagist.org/packages/benbjurstrom/otpz)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/benbjurstrom/otpz/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/benbjurstrom/otpz/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/benbjurstrom/otpz/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/benbjurstrom/otpz/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/benbjurstrom/otpz.svg?style=flat-square)](https://packagist.org/packages/benbjurstrom/otpz)

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/otpz.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/otpz)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation

You can install the package via composer:

```bash
composer require benbjurstrom/otpz
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="otpz-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="otpz-config"
```

This is the contents of the published config file:

```php
return [
];
```

Finally add the HasOtpz trait to the user model:

```php
<?php

namespace App\Models;

...
use BenBjurstrom\Otpz\Models\Concerns\HasOtps;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasOtps;
    
    // ...
}
```

```bash
php artisan vendor:publish --tag="otpz-views"
```

## Usage

```php
$otpz = new BenBjurstrom\Otpz();
echo $otpz->echoPhrase('Hello, BenBjurstrom!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Ben Bjurstrom](https://github.com/benbjurstrom)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Purpose

This package is designed to provide a secure and easy-to-use first factor one-time password (OTP) system for Laravel applications. These OTPs are for authentication purposes and are intended to be used instead of the traditional password.

### But OTPs are less secure than passwords!
You're right, the typical 6 digit OTPs are less secure than passwords as they only contain 10^6 possible combinations. But this package uses 9 case-insensitive alphanumeric characters for each OTP, which provides 36^9 possible combinations. Or about 100 trillion possible combinations.

Additionally, the otp's generated by this package are only valid 5 minutes. This means that an attacker has a limited window to use the OTP before it expires.

Passwords, on the other hand, are valid indefinitely. If an attacker gains access to a user's password, they can use it to access the user's account at any time in the future.

Additionally, OTPs are only valid for a single use. If an attacker intercepts an OTP, they can't use it to access the user's account in the future.

Additionally, OTPs are invalidated after 3 failed attempts. So an attacker can't keep trying OTPs until they find the correct one.

### But email's not secure!
You're right, email is not a secure medium. But remember first factor OTPs are intended to replace passwords. And every password based system offers a "forgot password" feature that sends a password reset link to the user's email. 

This package is no different. If an attacker has access to a user's email account, they can easily request a one time password and gain access to their account. The same is true for any system that uses email for password resets.

If your application requires elevated security, you should consider adding a second factor on top of this package such as Time-based One-Time Passwords (TOTP) or Passkeys.

### The trouble with magic links
Pure magic links are arguably more secure then the one time passwords used by this package as an attacker is much less likely to brute force a signed URL than a 9 character alphanumeric string. However, there's a usibility tradeoff. 

Magic links are sent to the email address associated with the user's account. Therefore, the user must have access to their email account on the device they wish to login to in order to click the link. On personal devices this is typically not an issue, but on shared devices or public computers this can be a problem.

Now before logging into your application, the user must first sign into their email account. This is a significant usability tradeoff.

By contrast, the secure OTPs generated by this package could be read on the user's phone and entered into the application on a shared device. This is a much more user-friendly experience.
