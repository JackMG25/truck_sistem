<?php

namespace App\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // DomPDF necesita carpetas escribibles en storage (común en hosting compartido).
        foreach ([
            storage_path('app/dompdf/fonts'),
            storage_path('app/dompdf/tmp'),
        ] as $directory) {
            if (! File::isDirectory($directory)) {
                File::makeDirectory($directory, 0755, true);
            }
        }
    }
}
