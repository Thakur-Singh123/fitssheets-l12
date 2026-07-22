<?php

namespace App\LegacyExcel;

class ExcelManager
{
    public function create(string $filename, callable $callback): LegacyExcelWorkbook
    {
        $workbook = new LegacyExcelWorkbook($filename);
        $callback($workbook);

        return $workbook;
    }
}
