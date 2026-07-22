<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \App\LegacyExcel\LegacyExcelWorkbook create(string $filename, callable $callback)
 */
class Excel extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'legacy-excel';
    }
}
