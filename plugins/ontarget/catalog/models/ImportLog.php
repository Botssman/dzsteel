<?php namespace OnTarget\Catalog\Models;

use Model;

/**
 * ImportLog Model
 *
 * @link https://docs.octobercms.com/3.x/extend/system/models.html
 */
class ImportLog extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string table name
     */
    public $table = 'ontarget_catalog_import_logs';

    /**
     * @var array rules for validation
     */
    public $rules = [];

    public $dates = ['finished_at'];

    public $jsonable = [
        'results', 'product_data'
    ];

    public $belongsTo = [
        'category' => Category::class
    ];

    public function beforeSave()
    {
//        if (count($this->results ?? []) == count($this->product_data ?? [])) {
//            $this->finished_at = now()->format('Y-m-d H:i:s');
//            $this->status = 'finished';
//        }
    }

    public function getTotalCountAttribute()
    {
        return count($this->results ?? []);
    }
}
