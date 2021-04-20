<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Класс для генерация *.xlsx документа
 */
class XlsExchange
{
    private Spreadsheet $phpExcel;

    private Worksheet $activeSheet;

    private string $xlsFileName;

    private string $tempXlsxPath;

    public function __construct(string $exportFileName)
    {
        $this->xlsFileName = $exportFileName;
        $this->setTempXlsPath();

        $this->phpExcel = new Spreadsheet();
        $this->activeSheet = $this->phpExcel->getActiveSheet();

        $this->setUpActiveSheet();
    }

    /**
     *  Настройки листа документа xlsx
     */
    private function setUpActiveSheet()
    {
        $styleHeader = [
            'font' => [
                'bold' => true,
            ]
        ];
        $this->activeSheet->getStyle('A1:BB1')->applyFromArray($styleHeader);
    }

    public function renderExcel(array $data): self
    {
        $this->fillContentTableHeaders();
        $this->fillTableData($data);

        return $this;
    }

    /**
     *  Заполнение заголовка таблицы
     */
    private function fillContentTableHeaders(): void
    {
        $this->activeSheet->setCellValue('A1', 'id');
        $this->activeSheet->setCellValue('B1', 'ШК');
        $this->activeSheet->setCellValue('C1', 'Название');
        $this->activeSheet->setCellValue('D1', 'Кол-во');
        $this->activeSheet->setCellValue('E1', 'Сумма');
    }

    /**
     *  Заполнение таблицы данными
     */
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

    public function saveFile(StorageInterface $saver): self
    {
        $this->saveXlsToTmpDir();
        $saver->save($this->tempXlsxPath);

        return $this;
    }

    private function saveXlsToTmpDir(): void
    {
        $writer = new Xlsx($this->phpExcel);
        $writer->save($this->tempXlsxPath);
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