<?php namespace OnTarget\Catalog\Models;

use Model;
use October\Rain\Database\Traits\Validation;

/**
 * Order Model
 *
 * @link https://docs.octobercms.com/3.x/extend/system/models.html
 */
class Order extends Model
{
    use Validation;

    /**
     * @var string table name
     */
    public $table = 'ontarget_catalog_orders';

    /**
     * @var array rules for validation
     */
    public $rules = [];
}
