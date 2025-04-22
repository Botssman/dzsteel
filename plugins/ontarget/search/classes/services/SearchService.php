<?php

namespace OnTarget\Search\Classes\Services;

use Illuminate\Support\Collection;
use \October\Rain\Support\Collection as RainLabCollection;
use OnTarget\Catalog\Models\Category;

class SearchService
{
    private string $operator;

    public function __construct()
    {
        $this->operator = env('DB_CONNECTION') == 'pgsql' ? 'ilike' : 'like';
    }
    public function search(string $query): Collection|RainLabCollection
    {
        return $this->searchQuery($query);
    }

    private function searchQuery(string $query): \Illuminate\Database\Eloquent\Collection|array
    {
        return Category::query()
            ->select('ontarget_catalog_categories.*')
            ->where(function($q) use ($query) {
                $q->where('ontarget_catalog_categories.name', $this->operator, "%{$query}%")
                    ->orWhere('ontarget_catalog_categories.description', $this->operator, "%{$query}%");
            })
            ->orWhereHas('products', function($q) use ($query) {
                $q->where('ontarget_catalog_products.name', $this->operator, "%{$query}%")
                    ->orWhere('ontarget_catalog_products.description', $this->operator, "%{$query}%")
                    ->orWhere('ontarget_catalog_products.vendor_code', $this->operator, "%{$query}%");
            })
            ->with(['products' => function($q) use ($query) {
                $q->where(function($q) use ($query) {
                    $q->where('name', $this->operator, "%{$query}%")
                        ->orWhere('description', $this->operator, "%{$query}%")
                        ->orWhere('vendor_code', $this->operator, "%{$query}%");
                })
                    ->limit(10);
            }])
            ->get();
    }
}
