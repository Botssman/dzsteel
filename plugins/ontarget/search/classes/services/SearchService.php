<?php

namespace OnTarget\Search\Classes\Services;

use Illuminate\Support\Collection;
use \October\Rain\Support\Collection as RainLabCollection;
use OnTarget\Catalog\Models\Category;
use OnTarget\Catalog\Models\Product;

class SearchService
{
    private string $operator;

    private int $productsLimit = 1;

    public function __construct()
    {
        $this->operator = env('DB_CONNECTION') == 'pgsql' ? 'ilike' : 'like';
    }
    public function search(string $query): Collection|RainLabCollection
    {
        return $this->searchQuery($query);
    }

    public function searchByCategory(string $query, int|null $categoryId = null): \Illuminate\Contracts\Pagination\CursorPaginator
    {
        return $this->searchByCategoryQuery($query, $categoryId);
    }

    private function searchByCategoryQuery(string $query, int|null $categoryId = null): \Illuminate\Contracts\Pagination\CursorPaginator
    {
        return Product::query()
            ->where('category_id', $categoryId)
            ->where('name', $this->operator, "%{$query}%")
            ->orWhere('slug', $this->operator, "%{$query}%")
            ->orWhere('vendor_code', $this->operator, "%{$query}%")
            ->cursorPaginate(1);
    }

    private function searchQuery(string $query): \Illuminate\Database\Eloquent\Collection|array
    {
        return Category::query()
            ->select('ontarget_catalog_categories.*')
            ->where(function($q) use ($query) {
                $q->where('ontarget_catalog_categories.name', $this->operator, "%{$query}%");
            })
            ->orWhereHas('products', function($q) use ($query) {
                $q->where('ontarget_catalog_products.name', $this->operator, "%{$query}%")
                    ->orWhere('ontarget_catalog_products.slug', $this->operator, "%{$query}%")
                    ->orWhere('ontarget_catalog_products.vendor_code', $this->operator, "%{$query}%");
            })
            ->with(['products' => function($q) use ($query) {
                $q->where(function($q) use ($query) {
                    $q->where('name', $this->operator, "%{$query}%")
                        ->orWhere('slug', $this->operator, "%{$query}%")
                        ->orWhere('vendor_code', $this->operator, "%{$query}%");
                })
                    ->orderBy('name');
            }])
            ->limit(10)
            ->get()
            ->map(function($category) {
                $category->setRelation('products', $category->products->take($this->productsLimit));
                return $category;
            });
    }
}
