<?php

namespace OnTarget\Catalog\Classes\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use OnTarget\Catalog\Models\PropertyValue;

class DeleteDuplicatesJob implements ShouldQueue
{
    use Queueable;
    use InteractsWithQueue;


    public function handle()
    {
        $original = PropertyValue::find($this->duplicate->original_id);

        $duplicateValues = PropertyValue::where('slug', 'like', $original->slug . '%')
                                        ->where('id', '!=', $original->id)
                                        ->get();

        foreach ($duplicateValues as $duplicateValue) {
            $products = $duplicateValue->products;
            $original->products()->syncWithoutDetaching($products->pluck('id')->toArray());
            $duplicateValue->delete();
        }
    }
}
