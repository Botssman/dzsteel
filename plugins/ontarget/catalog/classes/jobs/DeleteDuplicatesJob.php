<?php

namespace OnTarget\Catalog\Classes\Jobs;

use Illuminate\Queue\Jobs\Job;
use OnTarget\Catalog\Models\PropertyValue;

class DeleteDuplicatesJob {
    public $timeout = 600;

    public $tries = 3;

    public $maxExceptions = 1;

    public function fire(Job $job, array $data)
    {
        try {
            // Находим оригинальную запись
            $original = PropertyValue::find($data['duplicate']['original_id']);
            if (!$original) return;

            // Находим все дубликаты для этого slug
            $duplicateValues = PropertyValue::where('slug', 'like', $original->slug . '-%')
            ->where('id', '!=', $original->id)
            ->orderBy('id')
            ->get();

            trace_log($duplicateValues->count());

            foreach ($duplicateValues as $duplicateValue) {
                \Queue::push(
                    ProcessSingleDuplicateJob::class,
                    [
                        'duplicate_id' => $duplicateValue->id,
                        'original_id' => $original->id,
                    ]
                );
            }


        } catch (\Throwable $exception) {
            trace_log($exception->getMessage());
            throw $exception;
        }
    }
}
