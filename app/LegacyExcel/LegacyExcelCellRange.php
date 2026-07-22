<?php

namespace App\LegacyExcel;

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LegacyExcelCellRange
{
    public function __construct(
        protected Worksheet $sheet,
        protected string $range
    ) {
    }

    public function setFontColor(string $color): void
    {
        $this->sheet->getStyle($this->range)->getFont()->getColor()->setARGB(LegacyExcelStyle::hexToArgb($color));
    }

    public function setFontFamily(string $family): void
    {
        $this->sheet->getStyle($this->range)->getFont()->setName($family);
    }

    public function setFontSize(int|float $size): void
    {
        $this->sheet->getStyle($this->range)->getFont()->setSize($size);
    }

    public function setBackground(string $color): void
    {
        $this->sheet->getStyle($this->range)->getFill()->setFillType(Fill::FILL_SOLID);
        $this->sheet->getStyle($this->range)->getFill()->getStartColor()->setARGB(LegacyExcelStyle::hexToArgb($color));
    }

    public function setAlignment(string $alignment): void
    {
        $map = [
            'center' => Alignment::HORIZONTAL_CENTER,
            'left' => Alignment::HORIZONTAL_LEFT,
            'right' => Alignment::HORIZONTAL_RIGHT,
        ];

        $this->sheet->getStyle($this->range)->getAlignment()->setHorizontal(
            $map[strtolower($alignment)] ?? Alignment::HORIZONTAL_GENERAL
        );
    }
}
