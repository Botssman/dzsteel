<?php namespace OnTarget\Catalog\Models;

use Model;
use October\Rain\Database\Builder;
use October\Rain\Database\Traits\Sluggable;
use October\Rain\Database\Traits\Sortable;
use October\Rain\Database\Traits\Validation;
use System\Models\File;

/**
 * Product Model
 *
 * @link https://docs.octobercms.com/3.x/extend/system/models.html
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @mixin Builder
 *
 * @method static \Illuminate\Database\Eloquent\Builder|static query()
 */
class Product extends Model
{
    use Validation;
    use Sortable;
    use Sluggable;

    /**
     * @var string table name
     */
    public $table = 'ontarget_catalog_products';

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
        'category_id' => ['integer','required'],
    ];

    public $belongsTo = [
        'category' => Category::class
    ];

    public $attachOne = [
        'image' => File::class,
    ];

    public $attachMany = [
        'images' => File::class,
        'documents' => File::class,
    ];
}
