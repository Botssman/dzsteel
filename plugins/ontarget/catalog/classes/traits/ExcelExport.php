<?php namespace OnTarget\Catalog\Classes\Traits;

use ApplicationException;
use Lang;

trait ExcelExport
{
    /**
     * @param $columns
     * @param $results
     * @param $options
     * @return string
     * @throws ApplicationException|\PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    protected function processExportData($columns, $results, $options): string
    {
        // Extend columns
        $columns = $this->exportExtendColumns($columns);

        // Save for download
        $fileName = uniqid('oc');

        // Prepare export
        if ($this->file_format === 'json') {
            $fileName .= 'xjson';
            $options['savePath'] = $this->getTemporaryExportPath($fileName);
            $this->processExportDataAsJson($columns, $results, $options);
        } elseif ($this->file_format === 'xlsx') {
            $fileName .= 'xxlsx';
            $options['savePath'] = $this->getTemporaryExportPath($fileName);
            $this->processExportDataAsExcel($columns, $results, $options);
        } else {
            $fileName .= 'xcsv';
            $options['savePath'] = $this->getTemporaryExportPath($fileName);
            $this->processExportDataAsCsv($columns, $results, $options);
        }

        return $fileName;
    }

    /**
     * @param $name
     * @param $outputName
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws ApplicationException
     */
    public function download($name, $outputName = null)
    {
        if (!preg_match('/^oc[0-9a-z]*$/i', $name)) {
            throw new ApplicationException(Lang::get('backend::lang.import_export.file_not_found_error'));
        }

        $filePath = $this->getTemporaryExportPath($name);
        if (!file_exists($filePath)) {
            throw new ApplicationException(Lang::get('backend::lang.import_export.file_not_found_error'));
        }

        $contentType = match(true) {
            str_ends_with($name, 'xjson') => 'application/json',
            str_ends_with($name, 'xxlsx') => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            default => 'text/csv',
        };

        /**
         * @TODO: шаблон именования файла
         */
        $outputName = str_ends_with($name, 'xxlsx') ? 'export.xlsx' : $outputName;

        return response()->download($filePath, $outputName, [
            'Content-Type' => $contentType,
        ])->deleteFileAfterSend(true);
    }

}
