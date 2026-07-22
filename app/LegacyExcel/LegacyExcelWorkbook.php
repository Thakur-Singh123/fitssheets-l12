<?php

namespace App\LegacyExcel;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LegacyExcelWorkbook
{
    protected Spreadsheet $spreadsheet;

    protected string $filename;

    public function __construct(string $filename)
    {
        $this->filename = $filename;
        $this->spreadsheet = new Spreadsheet();
    }

    public function setTitle(string $title): void
    {
        $this->spreadsheet->getProperties()->setTitle($title);
    }

    public function sheet(string $name, callable $callback): void
    {
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->setTitle(mb_substr($name, 0, 31));

        $callback(new LegacyExcelSheet($sheet));
    }

    public function download(string $format = 'xlsx'): StreamedResponse
    {
        $extension = strtolower($format);
        $downloadName = $this->filename.'.'.$extension;

        return response()->streamDownload(function () {
            $writer = new Xlsx($this->spreadsheet);
            $writer->save('php://output');
        }, $downloadName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
