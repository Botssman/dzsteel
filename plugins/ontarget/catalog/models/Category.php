<?php namespace OnTarget\Catalog\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Model;
use October\Rain\Database\Builder;
use October\Rain\Database\Traits\NestedTree;
use October\Rain\Database\Traits\Nullable;
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
 *
 * @property int $id
 * @property bool $is_active
 * @property int $sort_order
 * @property string $name
 * @property string $slug
 * @property string $description
 *
 * @property MeasureUnit $measure_unit
 * @property string $parent_path
 * @property string $measure_unit_name
 */
class Category extends Model
{
    use Validation;
    use NestedTree;
    use Sortable;
    use Sluggable;
    use Nullable;

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
    ];

    /**
     * @var string[]
     */
    public $nullable = [
        'description'
    ];

    /**
     * @var string[]
     */
    public $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * @var string[]
     */
    public $appends = [
        'parent_path',
        'measure_unit_name'
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

    /**
     * @return Attribute
     */
    protected function parentPath() : Attribute
    {
        return Attribute::make(
            get: fn () => $this->getParents()
                ->pluck('name')
                ->join('|')
        );
    }

    /**
     * @return Attribute
     */
    protected function measureUnitName() : Attribute
    {
        return Attribute::make(
            get: fn () => optional($this->measure_unit)->name
        );
    }

}
