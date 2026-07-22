<?php

namespace App\LegacyExcel;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LegacyExcelCell
{
    public function __construct(
        protected Worksheet $sheet,
        protected string $coordinate
    ) {
    }

    public function setValue(mixed $value): void
    {
        $this->sheet->setCellValue($this->coordinate, $value);
    }
}
