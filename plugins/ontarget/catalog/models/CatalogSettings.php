<?php namespace OnTarget\Catalog\Models;

class CatalogSettings extends \System\Models\SettingModel
{
    public $settingsCode = 'ontarget_catalog_settings';

    public $settingsFields = 'fields.yaml';

    public $jsonable = ['import_description_replaces'];
}
