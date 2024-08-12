<?php namespace OnTarget\Catalog\Controllers;

use Backend\Behaviors\FormController;
use Backend\Behaviors\ImportExportController;
use Backend\Behaviors\ListController;
use BackendMenu;
use Backend\Classes\Controller;
use OnTarget\Catalog\Behaviors\ImportBehavior;
use OnTarget\Catalog\Models\Category;
use OnTarget\Catalog\Models\Product;
use OnTarget\Catalog\Models\Property;

/**
 * Products Backend Controller
 *
 * @link https://docs.octobercms.com/3.x/extend/system/controllers.html
 */
class Products extends Controller
{
    public $implement = [
        FormController::class,
        ListController::class,
        ImportBehavior::class

    ];

    /**
     * @var string formConfig file
     */
    public $formConfig = 'config_form.yaml';

    public $importExportConfig = 'config_import_export.yaml';

    /**
     * @var string listConfig file
     */
    public $listConfig = 'config_list.yaml';

    /**
     * @var array required permissions
     */
    public $requiredPermissions = ['ontarget.catalog.products'];

    /**
     * __construct the controller
     */
    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('OnTarget.Catalog', 'catalog', 'products');
    }

    public function formAfterUpdate(Product $model)
    {
        $model->handlePropertyValueUpdates();
    }

    public function importExportExtendColumns(array $columns) : array
    {
        $category = Category::find(post('category_id'));

        if (empty($category)) return $columns;

        $properties =  $category->properties
            ->mapWithKeys(fn ($property) => ["_$property->slug" => "_$property->name"])
            ->toArray();

        return array_merge($columns, $properties);
    }
}
