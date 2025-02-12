<?php

namespace OnTarget\Catalog\Classes\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Jobs\Job;
use OnTarget\Catalog\Models\PropertyValue;

class ProcessSingleDuplicateJob {
    public $timeout = 600;

    public $tries = 5;

    public function fire(Job $job, array $data)
    {
        try {

            $original = PropertyValue::find($data['original_id']);
            $duplicateValue = PropertyValue::find($data['duplicate_id']);

            // Получаем все продукты, связанные с дубликатом
            $products = $duplicateValue->products;

            // Привязываем эти продукты к оригинальной записи
            $original->products()->syncWithoutDetaching($products->pluck('id')->toArray());

            // Удаляем дубликат
            $duplicateValue->delete();

        } catch (\Throwable $exception) {
            trace_log($exception->getMessage());
            throw $exception;
        }
    }
}
