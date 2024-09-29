<?php

namespace BenBjurstrom\Plink;

use BenBjurstrom\Plink\Commands\PlinkCommand;
use BenBjurstrom\Plink\Http\Controllers\GetPlinkController;
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
            Route::get('plink/{id}', GetPlinkController::class)
                ->name('plink.show')
                ->middleware('guest');

            Route::get('/mailable', function () {
                $plink = \BenBjurstrom\Plink\Models\Plink::find(8);

                return new \BenBjurstrom\Plink\Mail\PlinkMail($plink);
            });

        });

        return $this;
    }
}
