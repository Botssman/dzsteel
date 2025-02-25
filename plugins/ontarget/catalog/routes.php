<?php

use OnTarget\Catalog\Models\PropertyValue;

\Route::get('dup', function(){

    $slugs = [
        'en-10219',
        'tu-6-06-142',
        'en-10088',
        'gost-1050-2013',
        'gost-1133-71',
        'gost-13663-86',
        'gost-14955-77',
        'gost-19903-2015',
        'gost-21631-2019',
        'gost-2246-70',
        'gost-34028-2016',
        'gost-5582-75',
        'gost-7350-77',
        'gost-7417-75',
        'gost-8568-77',
        'gost-8732-78',
        'gost-8734-75',
    ];

    foreach ($slugs as $slug) {
        $duplicates = PropertyValue::query()
                                   ->selectRaw('MIN(slug) as slug') // Используем MIN(slug) для выбора одного значения
                                   ->selectRaw('MIN(id) as original_id')
                                   ->where('slug', 'LIKE', "{$slug}%")
                                   ->havingRaw('COUNT(*) > 1')
                                   ->get();

        foreach($duplicates as $duplicate){
            //\Queue::push(OnTarget\Catalog\Classes\Jobs\DeleteDuplicatesJob::class,['duplicate'=>$duplicate]);

            print_r($duplicate->slug);
        }
    }

});
