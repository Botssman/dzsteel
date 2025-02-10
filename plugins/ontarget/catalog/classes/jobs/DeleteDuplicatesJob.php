<?php

namespace OnTarget\Catalog\Classes\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Jobs\Job;
use OnTarget\Catalog\Models\PropertyValue;

class DeleteDuplicatesJob implements ShouldQueue
{
    use Queueable;
    use InteractsWithQueue;

    public $duplicate;


    public function fire(Job $job, array $data)
    {
        try {
            $original = PropertyValue::find($data['duplicate']['original_id']);
            if (!$original) return;
            $duplicateValues = PropertyValue::where('slug', 'like', $original->slug . '%')
                                            ->where('id', '!=', $original->id)
                                            ->get();

            foreach ($duplicateValues as $duplicateValue) {
                $products = $duplicateValue->products;
                $original->products()->syncWithoutDetaching($products->pluck('id')->toArray());
                $duplicateValue->delete();
            }
        } catch (\Throwable $exception) {
            trace_log($exception);
        }
    }
}
