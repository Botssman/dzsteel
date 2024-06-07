<?php namespace OnTarget\Catalog\Models;

use Model;
use October\Rain\Database\Traits\Validation;

/**
 * Customer Model
 *
 * @link https://docs.octobercms.com/3.x/extend/system/models.html
 */
class Customer extends Model
{
    use Validation;

    /**
     * @var string table name
     */
    public $table = 'ontarget_catalog_customers';

    /**
     * @var array rules for validation
     */
    public $rules = [];

    public $hasMany = [
        'orders' => Order::class
    ];
}
