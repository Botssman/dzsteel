<?php namespace OnTarget\Catalog\Models;

use Model;
use October\Rain\Database\Builder;
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

    public $fillable = [
        'slug', 'name', 'property_id'
    ];

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

    public $belongsToMany = [
        'products' => [
            Product::class,
            'table' => 'ontarget_catalog_product_property_value'
        ]
    ];

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeHasProducts(Builder $query): Builder
    {
        return $query->whereHas('products');

    }

    /**
     * @param  Builder  $query
     * @param  int  $categoryId
     * @return Builder
     */
    public function scopeHasProductsInCategory(Builder $query, int $categoryId): Builder
    {
        return $query->whereHas('products.category', function (Builder $q) use ($categoryId){
            return $q->where('category_id', $categoryId);
        });
    }
}
