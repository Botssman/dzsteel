<?php namespace OnTarget\Catalog\Models;

use Model;
use October\Rain\Database\Builder;
use October\Rain\Database\Traits\Sluggable;
use October\Rain\Database\Traits\Validation;
use OnTarget\Catalog\Classes\QueryBuilders\ProductQueryBuilder;
use System\Models\File;

/**
 * Property Model
 *
 * @link https://docs.octobercms.com/3.x/extend/system/models.html
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @mixin Builder
 *
 * @method static ProductQueryBuilder|static query()
 *
 * @method static ProductQueryBuilder|static forFilters()
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

    public $jsonable = ['extra'];

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

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeForFilters(Builder $query): Builder
    {
        return $query->where('show_in_filters', true);
    }
}
