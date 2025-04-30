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

    private int $categoryProductsLimit = 10;
    private int $productsLimit = 10;
    private int $quickSearchProductsLimit = 10;
    private int $quickSearchCategoriesLimit = 10;

    public function __construct()
    {
        $this->operator = env('DB_CONNECTION') == 'pgsql' ? 'ilike' : 'like';

        $this->categoryProductsLimit = CatalogSettings::get('search_category_products_per_page', 10);
        $this->productsLimit = CatalogSettings::get('search_products_per_page', 10);
        $this->quickSearchCategoriesLimit = CatalogSettings::get('quick_search_categories_per_page', 10);
        $this->quickSearchProductsLimit = CatalogSettings::get('quick_search_products_per_page', 10);
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
            ->search($query);

        $cursor = $this->normalizeCursor(request()->input('cursor', $cursor));

        return $builder->cursorPaginate(
            $perPage ?? $this->productsLimit,
            ['*'],
            'cursor',
            $cursor
        );
    }

    private function searchQuery(string $query): \Illuminate\Database\Eloquent\Collection|array
    {
        return Category::query()
            ->where(function($q) use ($query) {
                $q->search($query);
            })
            ->orWhereHas('products', function($q) use ($query) {
                $q->search("query");
            })
            ->with(['products' => function($q) use ($query) {
                $q->where(function($q) use ($query) {
                    $q->search("query");
                })
                    ->orderBy('name');
            }])
            ->get()
            ->map(function($category) {
                $category->setRelation('products', $category->products->take($this->categoryProductsLimit));
                return $category;
            });
    }

    public function quickSearch(string $query): \Illuminate\Database\Eloquent\Collection|array
    {
        return $this->quickSearchQuery($query);
    }

    private function quickSearchQuery(string $query): \Illuminate\Database\Eloquent\Collection|array
    {
        $categories = Category::query()
            ->search($query)
            ->limit($this->quickSearchCategoriesLimit)
            ->orderBy('name')
            ->get();


        $products = Product::query()
            ->search($query)
            ->limit($this->quickSearchProductsLimit)
            ->orderBy('name')
            ->with('category')
            ->get();

        return [
            'products' => $products,
            'categories' => $categories,
        ];
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
