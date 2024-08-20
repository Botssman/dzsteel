<?php namespace OnTarget\Catalog\Classes\Jobs;

use Illuminate\Queue\Jobs\Job;
use OnTarget\Catalog\Models\Category;
use OnTarget\Catalog\Models\ImportLog;
use OnTarget\Catalog\Models\ProductImport;

class ImportProductsJob
{
    /**
     * @param Job $job
     * @param array $data
     * @return void
     */
    public function fire(Job $job, array $data): void
    {
        try {
            $importModel = new ProductImport;
            $importModel->file_format = 'csv_custom';

            $importModel->category = Category::find($data['category_id']);

            $mappings = [
                'Название' => 'name',
                'Изображения' => 'images',
                'Изображение' => 'image',
                'Цена' => 'price',
                'Описание' => 'properties',
                'Категория' => 'category',
            ];

            $matches = $importModel->matchColumns($data['file_path'], $mappings);

            $importModel->importFile(
                $data['file_path'],
                [
                    'matches' => $matches,
                    'delimiter' => ';'
                ]
            );

            $job->delete();
        } catch (\Exception $exception) {
            trace_log($exception);
        }
    }
}
