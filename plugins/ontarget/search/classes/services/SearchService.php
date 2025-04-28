<?php

namespace OnTarget\Search\Classes\Services;

use Illuminate\Support\Collection;
use \October\Rain\Support\Collection as RainLabCollection;
use OnTarget\Catalog\Models\CatalogSettings;
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

    private function searchByCategoryQuery(
        string $query,
        ?int $categoryId = null,
        int $perPage = null,
        string $cursor = null
    ): \Illuminate\Contracts\Pagination\CursorPaginator {

        if (empty(trim($query))) {
            throw new \InvalidArgumentException('Search query cannot be empty');
        }

        $builder = Product::query()
            ->when($categoryId, function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->where(function ($queryBuilder) use ($query) {
                $queryBuilder
                    ->where('name', 'LIKE', "{$query}%")
                    ->orWhere('slug', 'LIKE', "{$query}%")
                    ->orWhere('vendor_code', 'LIKE', "{$query}%");
            });

        $cursor = $this->normalizeCursor(request()->input('cursor', $cursor));

        return $builder->cursorPaginate(
            $perPage ?? CatalogSettings::get('products_per_page', $this->productsLimit),
            ['*'],
            'cursor',
            $cursor
        );
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

    public function quickSearch(string $query): \Illuminate\Database\Eloquent\Collection|array
    {
        return $this->quickSearchQuery($query);
    }

    private function quickSearchQuery(string $query): \Illuminate\Database\Eloquent\Collection|array
    {
        return Product::query()
            ->select([
                    'ontarget_catalog_products.name',
                    'ontarget_catalog_products.slug',
                    'ontarget_catalog_products.media_image',
                    'ontarget_catalog_products.category_id',
            ])
            ->where('name', $this->operator, "%{$query}%")
            ->orWhere('slug', $this->operator, "%{$query}%")
            ->orWhere('vendor_code', $this->operator, "%{$query}%")
            ->limit(5)
            ->orderBy('name')
            ->with('category')
            ->get();
    }

    private function normalizeCursor(?string $cursor): ?string
    {
        if (empty($cursor)) {
            return null;
        }

        try {
            $decoded = base64_decode($cursor, true);
            if ($decoded === false) {
                return null;
            }

            $data = json_decode($decoded, true);
            if (!isset($data['id'], $data['created_at'])) {
                return null;
            }

            return $cursor;
        } catch (\Exception $e) {
            return null;
        }
    }
}
