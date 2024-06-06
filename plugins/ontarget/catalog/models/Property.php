<?php namespace OnTarget\Catalog\Models;

use Model;
use October\Rain\Database\Traits\Sluggable;
use October\Rain\Database\Traits\Validation;
use System\Models\File;

/**
 * Property Model
 *
 * @link https://docs.octobercms.com/3.x/extend/system/models.html
 */
class Property extends Model
{
    use Validation;
    use Sluggable;

    /**
     * Slugs for Sluggable trait
     * @var array|string[]
     */
    public array $slugs = [
        'slug' => 'name'
    ];

    /**
     * @var string table name
     */
    public $table = 'ontarget_catalog_properties';

    /**
     * @var array rules for validation
     */
    public $rules = [
        'name' => ['required'],
        'slug' => ['required'],
    ];

    public $belongsToMany = [
        'categories' => [
            Category::class,
            'table' => 'ontarget_catalog_category_property'
        ]
    ];

    public $hasMany = [
        'values' => PropertyValue::class
    ];

    public $attachOne = [
        'icon' => File::class
    ];
}
