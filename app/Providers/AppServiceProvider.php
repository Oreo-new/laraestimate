<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use KgBot\LaravelLocalization\Facades\ExportLocalizations;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    
        Schema::defaultStringLength(191);
        $localizationData = ExportLocalizations::export()->toFlat();

        View::composer('layouts.app', function ($view) use ($localizationData) {  
            return $view->with( ['localizationData' => $localizationData] );
        });

        View::composer('estimates.show', function ($view) use ($localizationData) {  
            return $view->with( ['localizationData' => $localizationData] );
        });
    }
}
