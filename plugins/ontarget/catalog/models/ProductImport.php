<?php

namespace OnTarget\Catalog\Models;

use Backend\Models\ImportModel;
use DOMDocument;
use DOMXPath;
use Event;
use Exception;
use October\Rain\Database\Builder;
use OnTarget\Catalog\Classes\QueryBuilders\ProductQueryBuilder;
use OnTarget\Catalog\Classes\Scopes\ActiveScope;
use OnTarget\Catalog\Classes\Traits\ExcelProcessor;
use OnTarget\Catalog\Classes\Jobs\ProcessProductJob;
use Queue;
use Str;
use System\Models\File;

class ProductImport extends ImportModel
{

    use ExcelProcessor;

    public Category $category;

    public Product $product;

    public array $productData;

    public $rules = [
        'name' => ['required', 'string', 'min:3', 'max:255'],
        'price' => ['sometimes', 'numeric', 'min:0'],
        'old_price' => ['sometimes', 'numeric', 'min:0'],
        'id' => ['sometimes', 'integer', 'exists:ontarget_catalog_products'],
    ];

    /**
     * @inheritDoc
     */
    public function importData($results, $sessionKey = null): void
    {
        Event::fire('ontarget.import.products.start', [&$results, $sessionKey]);

        if (empty($this->category)) {
            $this->category = Category::findOr(
                id: post('category_id'),
                callback: function() {
                    throw new \ApplicationException('Отсутствует категория!');
                }
            );
        }

        $importLog = new ImportLog;
        $importLog->category_id = $this->category->id;
        $importLog->status = "pending";
        $importLog->session_key = $sessionKey;
        $importLog->product_data = [];
        $importLog->results = [];
        $importLog->save();

        foreach ($results as $row => $data) {
            Event::fire('ontarget.import.products.row', [&$row, &$data, $importLog, $sessionKey]);

            try {
                Queue::push(ProcessProductJob::class, [
                    'category_id' => $this->category->id,
                    'import_log_id' => $importLog->id,
                    'product_data' => $data,
                    'row' => $row
                ]);
            } catch (\ValidationException $exception) {
                $this->logError($row, $exception->getMessage());
            } catch (Exception $exception) {
                $this->logError($row, $exception->getMessage());
                trace_log($exception);
            }

        }

        Event::fire('ontarget.import.products.end', [&$results, $sessionKey]);
    }

    public function getCategoryIdOptions()
    {
        return Category::query()
            ->tap(fn() => new ActiveScope)
            ->pluck( 'name', 'id');
    }

    public function matchColumns($csvFilePath, $columnMapping): array
    {
        if (($handle = fopen($csvFilePath, 'r')) !== false) {
            $headerRaw= fgetcsv($handle);

            $header = explode(';', $headerRaw[0]);

            $result = [];

            foreach ($header as $index => $label) {
                if (isset($columnMapping[$label])) {
                    $result[$index] = $columnMapping[$label];
                }
            }

            fclose($handle);

            return $result;
        } else {
            throw new Exception("Unable to open the CSV file.");
        }
    }
}
