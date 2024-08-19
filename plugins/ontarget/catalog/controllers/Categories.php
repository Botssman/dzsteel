<?php namespace OnTarget\Catalog\Controllers;

use Backend\Behaviors\FormController;
use Backend\Behaviors\ImportExportController;
use Backend\Behaviors\ListController;
use BackendMenu;
use Backend\Classes\Controller;
use OnTarget\Catalog\Classes\Jobs\ImportProductsJob;
use OnTarget\Catalog\Models\Product;

/**
 * Categories Backend Controller
 *
 * @link https://docs.octobercms.com/3.x/extend/system/controllers.html
 */
class Categories extends Controller
{
    public $implement = [
        FormController::class,
        ListController::class,
        ImportExportController::class
    ];

    /**
     * @var string formConfig file
     */
    public $formConfig = 'config_form.yaml';

    /**
     * @var string listConfig file
     */
    public $listConfig = 'config_list.yaml';

    public $importExportConfig = 'config_import_export.yaml';

    /**
     * @var array required permissions
     */
    public $requiredPermissions = ['ontarget.catalog.categories'];

    /**
     * __construct the controller
     */
    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('OnTarget.Catalog', 'catalog', 'categories');
    }

    public function relationAfterUpdate($relationName, $model)
    {
        if ($model instanceof Product) {
            $model->handlePropertyValueUpdates();
        }
    }

    public function onProcessImport()
    {
        $category = $this->widget->form->model;
        if (empty($category)) throw new \AjaxException('Model not found!');
        if (empty($category->import_url)) throw new \AjaxException('Необходимо указать ссылку на файл');

        $csv = file_get_contents($category->import_url);
        $importFileName = "import_" . now()->timestamp . '.csv';
        \Storage::disk('import')->put($importFileName, $csv);

        \Queue::push(ImportProductsJob::class, [
           'file_path' => temp_path('import/' . $importFileName),
            'category_id' => $category->id
        ]);

        \Flash::info('Импорт добавлен в очередь');
    }
}
