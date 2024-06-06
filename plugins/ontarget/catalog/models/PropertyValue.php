<?php namespace OnTarget\Catalog\Models;

use Model;
use October\Rain\Database\Traits\Sluggable;
use October\Rain\Database\Traits\Sortable;
use October\Rain\Database\Traits\Validation;

/**
 * PropertyValue Model
 *
 * @link https://docs.octobercms.com/3.x/extend/system/models.html
 */
class PropertyValue extends Model
{
    use Validation;
    use Sortable;
    use Sluggable;

    /**
     * @var string table name
     */
    public $table = 'ontarget_catalog_property_values';

    /**
     * Slugs for Sluggable trait
     * @var array|string[]
     */
    public array $slugs = [
        'slug' => 'name'
    ];

    /**
     * @var array rules for validation
     */
    public $rules = [
        'name' => ['required'],
        'slug' => ['required'],
    ];

    public $belongsTo = [
        'property' => Property::class
    ];

    public $hasMany = [
        'products' => [
            Product::class,
            'table' => 'ontarget_catalog_product_property_value'
        ]
    ];
}
