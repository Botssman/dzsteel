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
            DB::transaction(function () {
                trace_log(1);
                $duplicates = PropertyValue::query()
                                           ->select('property_id', 'name', DB::raw('MIN(id) as min_id'))
                                           ->groupBy('property_id', 'name')
                                           ->havingRaw('COUNT(*) > 1')
                                           ->get();
                trace_log(2);
                $usedPropertyValueIds = DB::table('ontarget_catalog_product_property_value')
                                          ->pluck('property_value_id')
                                          ->toArray();
                trace_log(3);
                foreach ($duplicates as $duplicate) {
                    trace_log(4);
                    $idsToDelete = PropertyValue::query()
                                                ->where('property_id', $duplicate->property_id)
                                                ->where('name', $duplicate->name)
                                                ->whereNotIn('id', $usedPropertyValueIds)
                                                ->where('id', '!=', $duplicate->min_id)
                                                ->pluck('id')
                                                ->toArray();
                    trace_log(5);
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
