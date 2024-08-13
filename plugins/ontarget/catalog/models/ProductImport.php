<?php

namespace OnTarget\Catalog\Models;

use Backend\Models\ImportModel;
use DOMDocument;
use DOMXPath;
use Exception;
use October\Rain\Database\Builder;
use OnTarget\Catalog\Classes\QueryBuilders\ProductQueryBuilder;
use OnTarget\Catalog\Classes\Scopes\ActiveScope;
use OnTarget\Catalog\Classes\Traits\ExcelProcessor;
use Str;
use System\Models\File;

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

        $this->product = Product::query()
            ->where('vendor_code', $data['vendor_code'])
            ->orWhere('id', $data['id'])
            ->firstOrNew();

        $isExisting = $this->product->exists;

        $this->product->name = $data['name'];
        $this->product->slug = $data['slug'] ?? str_slug($data['name']);
        $this->product->category_id = $this->category->id;
        $this->product->price = $data['price'];
        $this->product->vendor_code = $data['vendor_code'] ?? str_random(7);
        $this->product->save();

        if (!empty($data['properties'])) {
            $this->setProperties($this->extractProperties($data['properties']));
        }

        if (!empty($data['images'])) {
            $images = explode('|', $data['images']);

            foreach ($images as $image) {
                $file = new File();

                if (filter_var($image, FILTER_VALIDATE_URL)) {
                    $file->fromUrl($image);
                } else {
                    $file->fromFile(storage_path("app/media/import/{$image}"));
                }

                $this->product->images()->add($file);
            }
        }

        if (!empty($data['image'])) {
            $file = new File();

            if (filter_var($data['image'], FILTER_VALIDATE_URL)) {
                $file->fromUrl($data['image']);
            } else {
                $file->fromFile(storage_path("app/media/import/{$data['image']}"));
            }

            $this->product->image()->add($file);
        }

        if ($isExisting) {
            $this->logUpdated();
        } else {
            $this->logCreated();
        }

        return $this->product;
    }

    /**
     * @param array $properties
     * @return void
     */
    protected function setProperties(array $properties): void
    {
        foreach ($properties as $key => $value) {
            $propertySlug = Str::slug($key);
            $property = Property::query()
                ->where('slug', $propertySlug)
                ->first();

            if (empty($property)) {
                $property = new Property();
                $property->name = $key;
                $property->slug = $propertySlug;
                $property->save();

                $this->category->properties()->attach($property->id);
            }

            $propertyValueSlug = Str::slug($value);
            $propertyValue = PropertyValue::query()
                ->firstOrCreate(
                    [
                        'slug' => $propertyValueSlug,
                        'property_id' => $property->id
                    ],
                    [
                        'slug' => $propertyValueSlug,
                        'name' => $value,
                        'property_id' => $property->id
                    ]
                );
            $this->product->property_values()->attach($propertyValue);
        }
    }

    /**
     * @param $htmlString
     * @return array
     */
    public function extractProperties($htmlString): array
    {
        $dom = new DOMDocument('1.0', 'UTF-8');

        libxml_use_internal_errors(true);

        $dom->loadHTML('<?xml encoding="UTF-8">' . $htmlString, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        libxml_clear_errors();

        $xpath = new DOMXPath($dom);

        $nodes = $xpath->query("//ul/li");

        $properties = [];

        foreach ($nodes as $node) {
            $textContent = $node->textContent;

            list($key, $value) = explode(': ', $textContent);

            $properties[trim($key)] = trim($value);
        }

        return $properties;
    }


    public function getCategoryIdOptions()
    {
        return Category::query()
            ->tap(fn() => new ActiveScope)
            ->pluck( 'name', 'id');
    }
}
