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

            $importModel->importFile(
                $data['file_path'],
                [
                    'matches' => [
                        0 => 'image',
                        1 => 'name',
                        2 => 'vendor_code',
                        3 => 'price',
                        8 => 'properties',
                    ],
                    'delimiter' => ';'
                ]
            );

            $job->delete();
        } catch (\Exception $exception) {
            trace_log($exception);
        }
    }
}
