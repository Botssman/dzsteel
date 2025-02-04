<?php namespace OnTarget\Catalog\Updates;

use Db;
use Seeder;
use OnTarget\Catalog\Models\PropertyValue;


/**
 * ontarget_catalog_category_property Migration
 *
 * @link https://docs.octobercms.com/3.x/extend/database/structure.html
 */
class SeedRemoveDuplicatePropertyValues extends Seeder
{
    public function run()
    {
        try {
            \DB::transaction(function () {
                // Шаг 1: Найти дубликаты
                $duplicates = PropertyValue::query()
                                           ->select('property_id', 'name', \DB::raw('MIN(id) as min_id'))
                                           ->groupBy('property_id', 'name')
                                           ->havingRaw('COUNT(*) > 1')
                                           ->get();

                // Шаг 2: Удалить дубликаты, которые не привязаны к товарам
                foreach ($duplicates as $duplicate) {
                    PropertyValue::query()
                                 ->where('property_id', $duplicate->property_id)
                                 ->where('name', $duplicate->name)
                                 ->whereNotIn('id', function ($query) {
                                     $query->select('property_value_id')
                                           ->from('ontarget_catalog_product_property_value');
                                 })
                                 ->where('id', '!=', $duplicate->min_id)
                                 ->delete();
                }
            });
        } catch (\Throwable $exception) {
            trace_log($exception);
        }
    }
}
