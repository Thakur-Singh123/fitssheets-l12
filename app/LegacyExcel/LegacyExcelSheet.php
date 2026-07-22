<?php

namespace App\LegacyExcel;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LegacyExcelSheet
{
    public function __construct(protected Worksheet $sheet)
    {
    }

    public function fromArray(array $source, $nullValue = null, string $startCell = 'A1', bool $strictNullComparison = false, bool $formatData = true): void
    {
        $this->sheet->fromArray($source, $nullValue, $startCell, $strictNullComparison);
    }

    public function row(int $rowNumber, array $values): void
    {
        $columnIndex = 1;

        foreach ($values as $value) {
            $this->sheet->setCellValue(Coordinate::stringFromColumnIndex($columnIndex).$rowNumber, $value);
            $columnIndex++;
        }
    }

    public function cell(string $coordinate, callable $callback): void
    {
        $callback(new LegacyExcelCell($this->sheet, $coordinate));
    }

    public function cells(string $range, callable $callback): void
    {
        $callback(new LegacyExcelCellRange($this->sheet, $range));
    }
}
