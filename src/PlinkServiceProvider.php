<?php

namespace BenBjurstrom\Plink;

use BenBjurstrom\Plink\Commands\PlinkCommand;
use BenBjurstrom\Plink\Http\Controllers\GetPlinkController;
use BenBjurstrom\Plink\Mail\PlinkMail;
use BenBjurstrom\Plink\Models\Plink as PlinkModel;
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
            ->hasMigration('create_plinks_table');
        $this->registerPlinkRouteMacro();
    }

    protected function registerPlinkRouteMacro(): self
    {
        Route::macro('plinkRoutes', function () {
            Route::get('plink/{id}', GetPlinkController::class)
                ->name('plink.show')
                ->middleware('guest');

            if ($this->app->environment('local')) { // Only for local environment
                Route::get('/plink', function () {
                    $plink = PlinkModel::find(1);

                    return new PlinkMail($plink);
                });
            }
        });

        return $this;
    }
}
