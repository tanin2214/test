<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class XlsExchange
{

    private $phpExcel;

    private $activeSheet;

    private $xlsFileName;

    private $tempXlsxPath;

    public function __construct()
    {
        $this->phpExcel = new Spreadsheet();
        $this->activeSheet = $this->phpExcel->getActiveSheet();

        $this->setUpPhpExcel();
        $this->setUpActiveSheet();
    }

    private function setUpPhpExcel()
    {

    }

    private function setUpActiveSheet()
    {
        $styleHeader = [
            'font' => [
                'bold' => true,
            ]
        ];
        $this->activeSheet->getStyle('A1:BB1')->applyFromArray($styleHeader);
    }

    public function renderExcel(array $data) :void
    {
        $this->fillContentTableHeaders();
        $this->fillTableData($data);
    }

    private function fillContentTableHeaders(): void
    {
        $this->activeSheet->setCellValue('A1', 'id');
        $this->activeSheet->setCellValue('B1', 'ШК');
        $this->activeSheet->setCellValue('C1', 'Название');
        $this->activeSheet->setCellValue('D1', 'Кол-во');
        $this->activeSheet->setCellValue('E1', 'Сумма');
    }

    private function fillTableData(array $data): void
    {
        $startRow = 2;

        foreach ($data as $item) {
            $this->activeSheet->setCellValue("A$startRow", $item['id']);
            $this->activeSheet->setCellValue("B$startRow", $item['barcode']);
            $this->activeSheet->setCellValue("C$startRow", $item['name']);
            $this->activeSheet->setCellValue("D$startRow", $item['quantity']);
            $this->activeSheet->setCellValue("E$startRow", $item['amount']);

            $startRow++;
        }
    }

    private function saveXlsToTmpDir(): void
    {
        $this->setTempXlsPath();

        $writer = new Xlsx($this->phpExcel);
        $writer->save($this->tempXlsxPath);
    }

    public function saveFile(StorageInterface $saver): void
    {
        $this->saveXlsToTmpDir();
        $saver->save($this->tempXlsxPath);
    }

    public function setXlsFileName(string $exportFileName): void
    {
        $this->xlsFileName = $exportFileName;
    }

    private function setTempXlsPath()
    {
        $this->tempXlsxPath = $this->getTmpPathToFile();
    }

    private function getTmpPathToFile(): string
    {
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . $this->xlsFileName . '.xlsx';
    }
}