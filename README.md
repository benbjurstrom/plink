# OTPz (OT-Peasy): First Factor One-Time Passwords For Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/benbjurstrom/otpz.svg?style=flat-square)](https://packagist.org/packages/benbjurstrom/otpz)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/benbjurstrom/otpz/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/benbjurstrom/otpz/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/benbjurstrom/otpz/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/benbjurstrom/otpz/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/benbjurstrom/otpz.svg?style=flat-square)](https://packagist.org/packages/benbjurstrom/otpz)

This package replaces the Laravel Breeze password configuration with first factor one-time passwords (OTP).



## Features

### Security
| ✅ System Details          | ✅ Expiration     | ✅ Issuance Limits |
|---------------------------|-------------------|---------------------|
| 36^9 possible combinations | After 5 minutes   | 1 every minute      |
| Bcrypt hashed             | After 3 attempts  | 3 every 5 minutes   |
| Auditable logs            | After 1 login     | 5 every 30 minutes  |

### Useablity

| ✅ Better than Passwords   | ✅ Easy to Use          |
|---------------------------|---------------------------|
| No memorization required  | Hyphenated for readablity |
| Eliminates password reuse | Case-insensitive          |
| No password reset flow    | Clickable login link      |

## FAQ
Why not use signed magic links?
- Magic links require the user to have access to their email account on the device they wish to login with. This is a significant usability tradeoff. OTPs can be read on the user's phone and entered into the application on a shared device.

Isn't email insecure?
- It's true that a compromised email account could be used to gain access to user accounts via this package. But remember first factor OTPs are intended to replace passwords. And most password based system offer a "forgot password" feature that sends a password reset link to the user's email.

What if a user loses access to their email?
- That's a legitimate concern. In a traditional password system, the user could continue using their password even though they've lost access to their email. For first factor OTPs, it's recommended to have either a backup email tied to their account or add something like passekys as an alternative auth method.

Why not just use passkeys?
- Passkeys offer the best security but require the user to have access to their password manager on the device they wish to login with. Though cross device authentication using passkey's is possible via scanning QR codes, the process is unreliable; at least in this developer's experience.

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
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Model Configuration
    |--------------------------------------------------------------------------
    |
    | This setting determines the model used by Otpz to store and retrieve
    | one-time passwords. By default, it uses the 'App\Models\User' model.
    |
    */
    
    'models' => [
        'authenticatable' => env('AUTH_MODEL', App\Models\User::class),
    ],
];

```

Update your User model to implement the Otpable interface with HasOtps trait

```php
<?php

namespace App\Models;

...
use BenBjurstrom\Otpz\Models\Concerns\HasOtps;
use BenBjurstrom\Otpz\Models\Concerns\Otpable;

class User extends Authenticatable implements Otpable
{
    use HasFactory, Notifiable, HasOtps;
    
    // ...
}
```

Add the Otp routes to your `routes/web.php` file

```php
Route::otpRoutes();

```

Finally add the Alpine.js Mask plugin to your guest.blade.php file

```html
<!-- Alpine Plugins -->
<script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/mask@3.x.x/dist/cdn.min.js"></script>
```

If you want to customize the views used by this package, you can publish them with:

```bash
php artisan vendor:publish --tag="otpz-views"
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
