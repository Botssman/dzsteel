<?php

namespace OnTarget\Catalog\Models;

use Backend\Models\ImportModel;
use Exception;
use OnTarget\Catalog\Classes\Scopes\ActiveScope;
use OnTarget\Catalog\Classes\Traits\ExcelProcessor;

class ProductImport extends ImportModel
{

    use ExcelProcessor;

    public Category $category;

    public Product $product;

    public array $productData;



    public $rules = [];

    /**
     * @inheritDoc
     */
    public function importData($results, $sessionKey = null)
    {
        if (empty(post('category_id'))) {
            throw new \ApplicationException('Не выбрана категория!');
        }

        $this->category = Category::find(post('category_id'));

        foreach ($results as $row => $data) {
            try {
                $this->processProduct($data);
            } catch (\ValidationException $exception) {
                $this->logError($row, $exception->getMessage());
            } catch (Exception $exception) {
                $this->logError($row, $exception->getMessage());
                trace_log($exception);
            }
        }
    }

    public function processProduct(array $data) : Product
    {
        $this->productData = $data;

        $this->product = !empty($data['id']) ?
            Product::findOrNew($data['id']) :
            new Product();

        $isExisting = $this->product->exists;

        $this->product->name = $data['name'];
        $this->product->slug = $data['slug'] ?? str_slug($data['name']);
        $this->product->category_id = $this->category->id;
        $this->product->price = $data['price'];
        $this->product->vendor_code = $data['vendor_code'] ?? str_random(7);
        $this->product->save();

        if ($isExisting) {
            $this->logUpdated();
        } else {
            $this->logCreated();
        }

        return $this->product;
    }

    public function getCategoryIdOptions()
    {
        return Category::query()
            ->tap(fn() => new ActiveScope)
            ->pluck( 'name', 'id');
    }
}
