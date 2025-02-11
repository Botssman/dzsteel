<?php

use OnTarget\Catalog\Models\PropertyValue;

\Route::get('dup', function(){

    $duplicates = PropertyValue::query()
                               ->selectRaw('MIN(slug) as slug') // Используем MIN(slug) для выбора одного значения
                               ->selectRaw('MIN(id) as original_id')
                               ->groupBy(\DB::raw("REGEXP_REPLACE(slug, '-\\d+$', '')"))
                               ->havingRaw('COUNT(*) > 1')
                               ->get();

foreach($duplicates as $duplicate){
\Queue::push(OnTarget\Catalog\Classes\Jobs\DeleteDuplicatesJob::class,['duplicate'=>$duplicate]);
}

});
