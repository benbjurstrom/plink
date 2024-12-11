<div align="center">
    <img src="https://github.com/benbjurstrom/plink/blob/main/art/plink.png?raw=true" alt="Plink Screenshot">
</div>

# Passwordless Log-In Links for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/benbjurstrom/plink.svg?style=flat-square)](https://packagist.org/packages/benbjurstrom/plink)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/benbjurstrom/plink/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/benbjurstrom/plink/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/benbjurstrom/plink/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/benbjurstrom/plink/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)

This package provides full-featured passwordless log-in links for Laravel applications.

- ✅ Rate limited
- ✅ Invalidated after first use
- ✅ Locked to the user's session
- ✅ Configurable expiration
- ✅ Detailed error messages
- ✅ Customizable mail template
- ✅ Auditable logs

## Installation

### 1. Install the package via composer

```bash
composer require benbjurstrom/plink
```

### 2. Add the package's interface and trait to your Authenticatable model

```php
// app/Models/User.php
namespace App\Models;

//...
use BenBjurstrom\Plink\Models\Concerns\HasPlinks;
use BenBjurstrom\Plink\Models\Concerns\Plinkable;

class User extends Authenticatable implements Plinkable
{
    use HasFactory, Notifiable, HasPlinks;
    
    // ...
}
```

### 3. Publish and run the migrations

```bash
php artisan vendor:publish --tag="plink-migrations"
php artisan migrate
```

### 4. Add the package provided routes

```php
// routes/web.php
Route::plinkRoutes();
```

### 5. (Optional) Publish the views for custom styling

```bash
php artisan vendor:publish --tag="plink-views"
```

This package publishes the following views:
```bash
resources/
└── views/
    └── vendor/
        └── plink/
            ├── error.blade.php
            ├── components/
                └── template.blade.php
            └── mail/
                ├── notification.blade.php
                └── plink.blade.php
```

### 6. (Optional) Publish the config file

```bash
php artisan vendor:publish --tag="plink-config"
```

This is the contents of the published config file:

```php
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Link Expiration and Throttling
    |--------------------------------------------------------------------------
    |
    | These settings control the security aspects of the generated links,
    | including their expiration time and the throttling mechanism to prevent
    | abuse.
    |
    */

    'expiration' => 5, // Minutes

    'limits' => [
        ['limit' => 1, 'minutes' => 1],
        ['limit' => 3, 'minutes' => 5],
        ['limit' => 5, 'minutes' => 30],
    ],

    /*
    |--------------------------------------------------------------------------
    | Model Configuration
    |--------------------------------------------------------------------------
    |
    | This setting determines the model used by Plink to store and retrieve
    | one-time passwords. By default, it uses the 'App\Models\User' model.
    |
    */

    'models' => [
        'authenticatable' => env('AUTH_MODEL', App\Models\User::class),
    ],

    /*
    |--------------------------------------------------------------------------
    | Mailable Configuration
    |--------------------------------------------------------------------------
    |
    | This setting determines the Mailable class used by Plink to send emails.
    | Change this to your own Mailable class if you want to customize the email
    | sending behavior.
    |
    */

    'mailable' => BenBjurstrom\Plink\Mail\PlinkMail::class,

    /*
    |--------------------------------------------------------------------------
    | Template Configuration
    |--------------------------------------------------------------------------
    |
    | This setting determines the email template used by Plink to send emails.
    | Switch to 'plink::mail.notification' if you prefer to use the default 
    | Laravel notification template.
    |
    */

    'template' => 'plink::mail.plink',
    // 'template' => 'plink::mail.notification',
];
```

## Usage

### Laravel Breeze Livewire Example
1. Replace the Breeze provided [App\Livewire\Forms\LoginForm::authenticate](https://github.com/laravel/breeze/blob/2.x/stubs/livewire-common/app/Livewire/Forms/LoginForm.php) method with a sendEmail method that runs the SendPlink action. Also be sure to remove password from the LoginForm's properties.
```php
    // app/Livewire/Forms/LoginForm.php

    use BenBjurstrom\Plink\Actions\SendPlink;
    use BenBjurstrom\Plink\Exceptions\PlinkThrottleException;
    use BenBjurstrom\Plink\Models\Plink;
    //...

    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('boolean')]
    public bool $remember = false;
    //...

    public function sendEmail(): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();
        RateLimiter::hit($this->throttleKey(), 300);

        try {
            (new SendPlink)->handle($this->email, $this->remember);
        } catch (PlinkThrottleException $e) {
            throw ValidationException::withMessages([
                'form.email' => $e->getMessage(),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }
```

2. Update [resources/views/livewire/pages/auth/login.blade.php](https://github.com/laravel/breeze/blob/2.x/stubs/livewire/resources/views/livewire/pages/auth/login.blade.php) such that the login function calls our new sendEmail method and redirects back with a status confirmation. You can also remove the password input field in this same file.
```php
    public function login(): void
    {
        $this->validate();

        $this->form->sendEmail();

        redirect()->back()->with(['status' => 'Login link sent!']);
    }
```

### Laravel Breeze Inertia Example
1. Replace the Breeze provided [App\Http\Requests\Auth\LoginRequest::authenticate](https://github.com/laravel/breeze/blob/e05ae1a21954c8d83bb0fcc78db87f157c16ac6c/stubs/default/app/Http/Requests/Auth/LoginRequest.php) method with a sendEmail method that runs the SendPlink action. Also be sure to remove password from the rules array.

```php
    // app/Http/Requests/Auth/LoginRequest.php

    use BenBjurstrom\Plink\Actions\SendPlink;
    use BenBjurstrom\Plink\Exceptions\PlinkThrottleException;
    use BenBjurstrom\Plink\Models\Plink;
    //...
    
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email']
        ];
    }
    //...
    
    public function sendEmail(): Void
    {
        $this->ensureIsNotRateLimited();
        RateLimiter::hit($this->throttleKey(), 300);

        try {
            (new SendPlink)->handle($this->email, $this->remember);
        } catch (PlinkThrottleException $e) {
            throw ValidationException::withMessages([
                'email' => $e->getMessage(),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }
```

2. Update the [App\Http\Controllers\Auth\AuthenticatedSessionController::store](https://github.com/laravel/breeze/blob/e05ae1a21954c8d83bb0fcc78db87f157c16ac6c/stubs/inertia-common/app/Http/Controllers/Auth/AuthenticatedSessionController.php) method to call our new sendEmail method and redirect back with a status confirmation.

```php
    // app/Http/Controllers/Auth/AuthenticatedSessionController.php

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->sendEmail();

        return back()->with(['status' => 'Login link sent!']);
    }
```

3. Remove the password input field from the [resources/js/Pages/Auth/Login.vue](https://github.com/laravel/breeze/blob/e05ae1a21954c8d83bb0fcc78db87f157c16ac6c/stubs/inertia-vue/resources/js/Pages/Auth/Login.vue) file.

Everything else is handled by the package components.

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
