<?php namespace OnTarget\Catalog\Models;

use Model;
use October\Rain\Database\Builder;
use October\Rain\Database\Traits\NestedTree;
use October\Rain\Database\Traits\Sluggable;
use October\Rain\Database\Traits\Sortable;
use October\Rain\Database\Traits\Validation;
use System\Models\File;

/**
 * Category Model
 *
 * @link https://docs.octobercms.com/3.x/extend/system/models.html
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @mixin Builder
 *
 * @method static \Illuminate\Database\Eloquent\Builder|static query()
 *
 * @method Builder|static active()
 */
class Category extends Model
{
    use Validation;
    use NestedTree;
    use Sortable;
    use Sluggable;

    /**
     * @var string table name
     */
    public $table = 'ontarget_catalog_categories';

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

    public $belongsToMany = [
        'properties' => [
            Property::class,
            'table' => 'ontarget_catalog_category_property'
        ]
    ];

    public $belongsTo = [
        'measure_unit' => MeasureUnit::class
    ];

    public $hasMany = [
        'products' => Product::class
    ];

    public $attachOne = [
        'image' => File::class,
        'icon' => File::class,
    ];

    public $attachMany = [
        'images' => File::class
    ];

}
