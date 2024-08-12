<?php namespace OnTarget\Catalog\Behaviors;

use ApplicationException;
use Backend\Behaviors\ImportExportController;
use Backend\Classes\Controller;
use OnTarget\Catalog\Classes\Traits\ExcelProcessor;
use League\Csv\Reader;
use October\Rain\Database\Models\DeferredBinding;
use System\Models\File;

class ImportBehavior extends ImportExportController
{
    use ExcelProcessor;

    /**
     * @param Controller $controller
     */
    public function __construct(Controller $controller)
    {
        parent::__construct($controller);
        $this->viewPath = base_path() . '/modules/backend/behaviors/importexportcontroller/partials';
        $this->assetPath = '/modules/backend/behaviors/importexportcontroller/assets';
    }

    /**
     * @param string $path
     * @return Reader
     */
    protected function createCsvReader(string $path) : Reader
    {
        try {
            $path = $this->processExcelToCsv($path);
        } catch (ApplicationException $exception) {
            trace_log($exception->getMessage());
        }

        return parent::createCsvReader($path);
    }

    /**
     * @return File
     */
    private function getFileModel(): File
    {
        $sessionKey = $this->importUploadFormWidget->getSessionKey();

        $deferredBinding = DeferredBinding::where('session_key', $sessionKey)
            ->orderBy('id', 'desc')
            ->where('master_field', 'import_file')
            ->first();

        return $deferredBinding->slave_type::find($deferredBinding->slave_id);
    }
}
