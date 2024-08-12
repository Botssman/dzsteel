<?php namespace OnTarget\Catalog\Classes\Traits;

use ApplicationException;
use BOXX\ImportExport\Classes\Enums\Column;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use System\Models\File;

trait ExcelProcessor
{
    /**
     * @throws ApplicationException
     */
    private function processExcelToCsv(string $path): string
    {
        $ext = pathinfo($path, PATHINFO_EXTENSION);

        if ($ext === 'csv' || mime_content_type($path) === 'text/csv' || mime_content_type($path) === 'text/plain') {
            return $path;
        }

        $tempCsvPath = $path . '.csv';

        $inputFileType = IOFactory::identify($path);

        try {
            $reader = IOFactory::createReader($inputFileType);
        } catch (Exception $e) {
            throw new ApplicationException('Unsupported file type: ' . $inputFileType);
        }

        $spreadsheet = $reader->load($path);
        $writer = new Csv($spreadsheet);
        $writer->setSheetIndex(0);
        $writer->save($tempCsvPath);

        $fileModel = $this->getFileModel();
        $disk = $fileModel->getDisk();
        $disk->put($fileModel->getDiskPath() . '.csv', file_get_contents($tempCsvPath));
        $fileModel->disk_name = $fileModel->disk_name . '.csv';
        $fileModel->save();

        return $path . '.csv';
    }

    /**
     * @param array $failed
     * @return File|null
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    private function makeErrorsFile(array $failed) : ?File
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        /**
         * Setting table header
         */
        $validationErrors = $failed['validation'] ?? [];
        $generalErrors = $failed['errors'] ?? [];
        $errors = array_merge($validationErrors, $generalErrors);

        if (empty($errors)) return null;

        $columnHeaders = array_keys($errors[0]['data']);
        $headerColumnIndex = 2;

        $sheet->setCellValue([1, 1], 'Ошибки');

        foreach ($columnHeaders as $header) {
            $headerValue = Column::tryFrom($header)?->label() ?? $header;
            $sheet->setCellValue([$headerColumnIndex, 1], $headerValue);
            $headerColumnIndex++;
        }

        /**
         * Filling in data
         */
        $rowIndex = 2;
        foreach ($errors as $row) {
            $dataColumnIndex = 2;

            $sheet->setCellValue([1, $rowIndex], $row['message']);

            foreach ($row['data'] as $value) {
                $sheet->setCellValue([$dataColumnIndex, $rowIndex], $value);
                $dataColumnIndex++;
            }
            $rowIndex++;
        }

        $writer = IOFactory::createWriter($spreadsheet, "Xlsx");
        $fileName = "errors_" . Carbon::now()->format('d_m_Y_h_i_s'). ".xlsx";
        $filePath = storage_path("app/import/errors/$fileName");
        $writer->save($filePath);

        return (new File())->fromFile($filePath);
    }

    /**
     * @param $columns
     * @param $results
     * @param $options
     * @return mixed
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function processExportDataAsExcel($columns, $results, $options)
    {
        $data = array_merge([$columns], $results);

        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $index = 1;
        foreach ($columns as  $col){
            $sheet->getStyle([$index, 1])->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('90CAF9');

            $sheet->getStyle([$index, 1])->getFont()->setBold(true);

            $index++;
        }

        $sheet->fromArray($data);

        $tempFilePath = $options['savePath'];
        $writer = new Xlsx($spreadsheet);

        $writer->save($tempFilePath);

        return $tempFilePath;
    }
}
