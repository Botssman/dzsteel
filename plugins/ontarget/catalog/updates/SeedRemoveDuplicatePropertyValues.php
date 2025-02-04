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
                $duplicates = PropertyValue::query()
                                           ->select('property_id', 'name', \DB::raw('MIN(id) as min_id'))
                                           ->groupBy('property_id', 'name')
                                           ->havingRaw('COUNT(*) > 1')
                                           ->get();

                $usedPropertyValueIds = [];

                \DB::table('ontarget_catalog_product_property_value')
                   ->select('property_value_id')
                   ->orderBy('property_value_id')
                   ->chunk(1000, function ($rows) use (&$usedPropertyValueIds) {
                       foreach ($rows as $row) {
                           $usedPropertyValueIds[] = $row->property_value_id;
                       }
                   });

                foreach ($duplicates as $duplicate) {
                    $idsToDelete = PropertyValue::query()
                                                ->where('property_id', $duplicate->property_id)
                                                ->where('name', $duplicate->name)
                                                ->whereNotIn('id', $usedPropertyValueIds)
                                                ->where('id', '!=', $duplicate->min_id)
                                                ->pluck('id')
                                                ->toArray();

                    if (!empty($idsToDelete)) {
                        foreach (array_chunk($idsToDelete, 1000) as $chunk) {
                            PropertyValue::query()
                                         ->whereIn('id', $chunk)
                                         ->delete();
                        }
                    }
                }
            });
        } catch (\Throwable $exception){
            trace_log($exception);
        }
    }
}
