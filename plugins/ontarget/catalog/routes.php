<?php

use OnTarget\Catalog\Models\PropertyValue;

\Route::get('dup', function(){

$duplicates = PropertyValue::query()
    ->select('slug')
    ->selectRaw('MIN(id) as original_id')
    ->groupBy('slug')
    ->havingRaw('COUNT(*) > 1')
    ->get();

foreach($duplicates as $duplicate){
\Queue::push(OnTarget\Catalog\Classes\Jobs\DeleteDuplicatesJob::class,['duplicate'=>$duplicate]);
}

});
