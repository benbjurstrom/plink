<?php

namespace BenBjurstrom\Otpz;

use BenBjurstrom\Otpz\Commands\OtpzCommand;
use BenBjurstrom\Otpz\Http\Controllers\GetLoginController;
use BenBjurstrom\Otpz\Http\Controllers\GetOtpController;
use BenBjurstrom\Otpz\Http\Controllers\PostLoginController;
use BenBjurstrom\Otpz\Http\Controllers\PostOtpController;
use BenBjurstrom\Otpz\Http\Controllers\PostOtpLinkController;
use Illuminate\Support\Facades\Route;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class OtpzServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('otpz')
            ->hasConfigFile()
            ->hasViews('otpz')
            ->hasMigration('create_otps_table')
            ->hasCommand(OtpzCommand::class);

        $this->registerOtpzRouteMacro();
    }

    protected function registerOtpzRouteMacro(): self
    {
        Route::macro('otpRoutes', function () {
            Route::get('login', GetLoginController::class)->name('login');

            Route::post('login', PostLoginController::class)->name('login.post');

            Route::get('login/{id}', GetOtpController::class)
                ->name('otp.show');

            Route::post('login/{id}', PostOtpController::class)
                ->name('otp.post');

            Route::get('otplink', PostOtpLinkController::class)
                ->name('otplink');
        });

        return $this;
    }
}
