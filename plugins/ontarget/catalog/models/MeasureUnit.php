<?php namespace OnTarget\Catalog\Models;

use Model;
use October\Rain\Database\Traits\Sluggable;
use October\Rain\Database\Traits\Validation;

/**
 * MeasureUnit Model
 *
 * @link https://docs.octobercms.com/3.x/extend/system/models.html
 */
class MeasureUnit extends Model
{
    use Validation;
    use Sluggable;

    /**
     * @var string table name
     */
    public $table = 'ontarget_catalog_measure_units';

    /**
     * Slugs for Sluggable trait
     * @var array|string[]
     */
    public array $slugs = [
        'slug' => 'name'
    ];

    public $fillable = ['name'];

    /**
     * @var array rules for validation
     */
    public $rules = [
        'name' => ['required'],
        'slug' => ['required'],
    ];
}
