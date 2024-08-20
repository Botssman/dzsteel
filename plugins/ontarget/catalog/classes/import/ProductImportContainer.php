<?php namespace OnTarget\Catalog\Classes\Import;

use Database\Tester\Models\Category;
use Exception;
use October\Rain\Database\Builder;
use OnTarget\Catalog\Models\Product;
use OnTarget\Catalog\Models\Property;
use OnTarget\Catalog\Models\PropertyValue;
use Str;
use System\Models\File;

class ProductImportContainer
{
    /**
     * @var Product
     */
    protected Product $product;

    protected Category $category;

    public string $mode = 'created';

    public function __construct(public array $data, public int $category_id)
    {
        $this->product = $this->makeProduct();
        $this->category = Category::find($this->category_id);
    }

    /**
     * @return Product
     * @throws Exception
     */
    public function process(): Product
    {
        $this->mode = $this->product->exists ? 'updated' : 'created';

        $this->product->category_id = $this->category_id;
        $this->product->name = $this->data['name'];
        $this->product->slug = $this->data['slug'] ?? str_slug($this->data['name']);
        $this->product->price = $this->data['price'];
        $this->product->vendor_code = $this->data['vendor_code'] ?? $this->makeVendorCode();
        $this->product->save();

        if (!empty($this->data['properties'])) {
            $this->setProperties($this->extractProperties($this->data['properties']));
        }

        if (!empty($this->data['images'])) {
            $images = explode('|', $this->data['images']);

            foreach ($images as $image) {
                $imageModel = $this->makeImage($image);
                $this->product->images()->add($imageModel);
            }
        }

        if (!empty($this->data['image'])) {
            $imageModel = $this->makeImage($this->data['image']);
            $this->product->image()->add($imageModel);
        }

        return $this->product;
    }

    /**
     * @return Product
     */
    protected function makeProduct() : Product
    {
        return Product::query()
            ->when(
                !empty($this->data['vendor_code']),
                fn (Builder $q) => $q->where('vendor_code', $this->data['vendor_code'])
            )
            ->when(
                !empty($this->data['id']),
                fn (Builder $q) => $q->orWhere('id', $this->data['id'])
            )
            ->firstOrNew();
    }

    /**
     * @param string $data
     * @return array
     */
    protected function extractProperties(string $data): array
    {
        $result = [];
        $lines = explode("\n", $data);

        foreach ($lines as $line) {
            $parts = explode(": ", $line, 2);

            if (count($parts) === 2) {
                $key = trim($parts[0]);
                $value = trim($parts[1]);
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * @param array $properties
     * @return void
     */
    protected function setProperties(array $properties): void
    {
        $propertyValuesIds = [];

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

            $propertyValuesIds[] = $propertyValue->id;

        }

        $this->product->property_values()->sync($propertyValuesIds);
    }

    /**
     * @throws Exception
     */
    protected function makeImage(string $path): File
    {
        $file = new File();

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            $file->fromUrl($path);
        } else {
            $file->fromFile(storage_path("app/media/import/{$path}"));
        }

        $file->save();
        return $file;

    }

    /**
     * @return string
     */
    protected function makeVendorCode() : string
    {
        return hash('sha256', microtime(true));
    }

}
