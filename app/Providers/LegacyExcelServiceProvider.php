<?php

namespace App\Providers;

use App\LegacyExcel\ExcelManager;
use Illuminate\Support\ServiceProvider;

class LegacyExcelServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('legacy-excel', fn () => new ExcelManager());
    }
}
