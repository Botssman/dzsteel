<?php

namespace OnTarget\Catalog\Classes\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Jobs\Job;
use OnTarget\Catalog\Models\PropertyValue;

class DeleteDuplicatesJob {
    public $timeout = 600;

    public $tries = 5;

    public function fire(Job $job, array $data)
    {
        try {

            // Находим оригинальную запись
            $original = PropertyValue::find($data['duplicate']['original_id']);
            if (!$original) return;

            // Находим все дубликаты для этого slug
            $duplicateValues = PropertyValue::where('slug', 'like', $original->slug . '%')
                                            ->where('id', '!=', $original->id)
                                            ->get();

            // Переносим связи с Product на оригинальную запись
            foreach ($duplicateValues as $duplicateValue) {
                // Получаем все продукты, связанные с дубликатом
                $products = $duplicateValue->products;

                // Привязываем эти продукты к оригинальной записи
                $original->products()->syncWithoutDetaching($products->pluck('id')->toArray());

                // Удаляем дубликат
                $duplicateValue->delete();
            }

        } catch (\Throwable $exception) {
            trace_log($exception->getMessage());
            throw $exception;
        }
    }
}
