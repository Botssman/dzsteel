<?php
use OnTarget\Catalog\Models\PropertyValue;

\Route::get('slugs', function (){
    OnTarget\Catalog\Models\PropertyValue::whereNull('original_slug')
                                         ->chunkById(200, function ($items) {
                                             $items->each(function ($item) {
                                                 $item->update(['original_slug' => Str::slug($item->name)]);
                                             });

                                             // Опционально: выводим прогресс
                                             echo "Обработано {$items->count()} записей\n";
                                         });
});

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






