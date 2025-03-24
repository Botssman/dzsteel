<?php
use OnTarget\Catalog\Models\PropertyValue;

\Route::get('dup', function(){

    $duplicates = PropertyValue::query()
                               ->select('original_slug', 'property_id') // Группируем по original_slug и property_id
                               ->selectRaw('MIN(id) as original_id') // Выбираем минимальный id в группе
                               ->groupBy('original_slug', 'property_id') // Группируем по original_slug и property_id
                               ->havingRaw('COUNT(*) > 1') // Оставляем только группы с дубликатами
                               ->get();

    foreach($duplicates as $duplicate){
        \Queue::push(OnTarget\Catalog\Classes\Jobs\DeleteDuplicatesJob::class,['duplicate'=>$duplicate]);
    }

    dd($duplicates->toArray());

});






