<?php namespace OnTarget\Catalog\Models;

use Model;

/**
 * Property Model
 *
 * @link https://docs.octobercms.com/3.x/extend/system/models.html
 */
class Property extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string table name
     */
    public $table = 'ontarget_catalog_properties';

    /**
     * @var array rules for validation
     */
    public $rules = [];
}
