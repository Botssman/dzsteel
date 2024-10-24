<?php namespace OnTarget\Catalog\Models;

use Model;
use October\Rain\Database\Builder;
use October\Rain\Database\Traits\Nullable;
use October\Rain\Database\Traits\Sluggable;
use October\Rain\Database\Traits\Sortable;
use October\Rain\Database\Traits\Validation;
use OnTarget\Catalog\Classes\QueryBuilders\ProductQueryBuilder;
use System\Models\File;

/**
 * Product Model
 *
 * @link https://docs.octobercms.com/3.x/extend/system/models.html
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @mixin Builder
 *
 * @method static ProductQueryBuilder|static query()
 *
 * @property File $preview
 */
class Product extends Model
{
    use Validation;
    use Sortable;
    use Sluggable;
    use Nullable;

    /**
     * @var string table name
     */
    public $table = 'ontarget_catalog_products';

    public $nullable = ['external_link', 'media_image', 'media_images'];
    public $jsonable = ['media_images'];

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
        'vendor_code' => ['required'],
        'category_id' => ['required', 'integer'],
        'price' => ['required', 'integer'],
    ];

    public $belongsTo = [
        'category' => Category::class,
        'measure_unit' => MeasureUnit::class
    ];

    public $belongsToMany = [
        'property_values' => [
            PropertyValue::class,
            'table' => 'ontarget_catalog_product_property_value'
        ],
    ];

    public $attachOne = [
        'image' => File::class,
    ];

    public $attachMany = [
        'images' => File::class,
        'documents' => File::class,
    ];

    public function handlePropertyValueUpdates()
    {
        $formData = array_wrap(post('PropertyValues', []));

        $formData = array_filter($formData, fn($i) => !empty($i));

        $this->property_values()->sync(array_values($formData));

    }

    /**
     * @param $query
     * @return ProductQueryBuilder
     */
    public function newEloquentBuilder($query): ProductQueryBuilder
    {
        return new ProductQueryBuilder($query);
    }

    /**
     * @return mixed|string
     */
    public function getPreviewAttribute()
    {
        return $this->image ?? $this->images()->first();
    }

    public function getImagePathAttribute()
    {
        if (!empty($this->image)) {
            return $this->image;
        }

        return $this->media_image;
    }
}
