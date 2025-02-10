<?php

namespace OnTarget\Catalog\Classes\Jobs;

use OnTarget\Catalog\Models\PropertyValue;

class DeleteDuplicatesJob
{
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
