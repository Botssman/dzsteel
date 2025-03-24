<?php

use OnTarget\Catalog\Models\PropertyValue;

\Route::get('dup', function(){

    $duplicates = PropertyValue::query()
                               ->selectRaw('original_slug') // Используем MIN(slug) для выбора одного значения
                               ->selectRaw('MIN(id) as original_id')
                               ->groupBy('original_slug')
                               ->havingRaw('COUNT(*) > 1')
                               ->get();

//foreach($duplicates as $duplicate){
\Queue::push(OnTarget\Catalog\Classes\Jobs\DeleteDuplicatesJob::class,['duplicate'=>$duplicate]);
//}

    dd($duplicates->toArray());

});
