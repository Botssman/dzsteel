<?php namespace OnTarget\Catalog\Controllers;

use Backend\Behaviors\FormController;
use Backend\Behaviors\ImportExportController;
use Backend\Behaviors\ListController;
use BackendMenu;
use Backend\Classes\Controller;
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
}
