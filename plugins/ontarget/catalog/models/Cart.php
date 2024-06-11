<?php namespace OnTarget\Catalog\Models;

use Model;

/**
 * Cart Model
 *
 * @link https://docs.octobercms.com/3.x/extend/system/models.html
 */
class Cart extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string table name
     */
    public $table = 'ontarget_catalog_carts';

    /**
     * @var array rules for validation
     */
    public $rules = [];
}
