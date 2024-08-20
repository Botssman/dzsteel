<?php namespace OnTarget\Catalog\Classes\Jobs;

use OnTarget\Catalog\Classes\Import\ProductImportContainer;
use OnTarget\Catalog\Models\ImportLog;
use OnTarget\Catalog\Models\ProductImport;
use TheSeer\Tokenizer\Exception;

class ProcessProductJob
{
    public function fire($job, $data): void
    {
        $container = new ProductImportContainer(
            $data['product_data'],
            $data['category_id']
        );
        $importLogEntry = ImportLog::find($data['import_log_id']);

        $importLogResults = $importLogEntry->results;

        $importLogRow = [
            'row' => $data['row'],
            'product_data' => $data['product_data'],
            'success' => false
        ];

        try {

            $product = $container->process();

            $importLogRow['success'] = true;
            $importLogRow['product'] = $product->toArray();
            $importLogRow['mode'] = $container->mode;
            $importLogRow['message'] = $container->mode == 'created' ? 'Создан' : 'Обновлён';

            $job->delete();
        } catch (\Exception $e) {

            $importLogRow['message'] = $e->getMessage() . PHP_EOL . "Подробности в журнале событий";
        }

        $importLogResults[] = $importLogRow;
        $importLogEntry->results = $importLogResults;
        $importLogEntry->save();
    }
}
