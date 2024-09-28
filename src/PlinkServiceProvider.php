<?php

namespace BenBjurstrom\Plink;

use BenBjurstrom\Plink\Commands\PlinkCommand;
use BenBjurstrom\Plink\Http\Controllers\GetLoginController;
use BenBjurstrom\Plink\Http\Controllers\GetOtpController;
use BenBjurstrom\Plink\Http\Controllers\PostLoginController;
use BenBjurstrom\Plink\Http\Controllers\PostOtpController;
use Illuminate\Support\Facades\Route;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class PlinkServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('plink')
            ->hasConfigFile()
            ->hasViews('plink')
            ->hasMigration('create_plinks_table')
            ->hasCommand(PlinkCommand::class);

        $this->registerPlinkRouteMacro();
    }

    protected function registerPlinkRouteMacro(): self
    {
        Route::macro('plinkRoutes', function () {
            Route::get('login', GetLoginController::class)->name('login');

            Route::post('login', PostLoginController::class)->name('login.post');

            Route::get('login/{id}', GetOtpController::class)
                ->name('plink.show');

            Route::post('login/{id}', PostOtpController::class)
                ->name('plink.post');
        });

        return $this;
    }
}
