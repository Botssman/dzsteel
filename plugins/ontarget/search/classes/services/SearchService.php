<?php

namespace OnTarget\Search\Classes\Services;

use Cache;
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

    private function searchQuery(string $query): \Illuminate\Database\Eloquent\Collection
    {
        $cacheKey = "search:categories:".md5($query).":limit:".$this->categoryProductsLimit;
        $cacheDuration = now()->addHours(6);

        return Cache::remember($cacheKey, $cacheDuration, function() use ($query) {
            $categories = Category::query()
                ->select(['id', 'name', 'slug'])
                ->where(function($q) use ($query) {
                    $q->search($query);
                })
                ->orWhereHas('products', function($q) use ($query) {
                    $q->search($query);
                })
                ->get();

            $categories->each(function($category) use ($query) {
                $productsCacheKey = "search:category:{$category->id}:products:".md5($query).":limit:".$this->categoryProductsLimit;

                $products = Cache::remember($productsCacheKey, now()->addHours(6), function() use ($category, $query) {
                    return $category->products()
                        ->selectRaw("DISTINCT ON (ontarget_catalog_products.id) ontarget_catalog_products.*,
        ts_rank(search_vector, websearch_to_tsquery('russian', ?)) as search_relevance,
        COALESCE(ontarget_catalog_products.rank, 0) as search_priority",
                            [$query]
                        )
                        ->whereRaw("search_vector @@ websearch_to_tsquery('russian', ?)", [$query])
                        ->orderBy('ontarget_catalog_products.id') // Должен быть ПЕРВЫМ для DISTINCT ON
                        ->orderByRaw(
                            "(ts_rank(search_vector, websearch_to_tsquery('russian', ?)) * COALESCE(ontarget_catalog_products.rank, 0)) DESC",
                            [$query]
                        )
                        ->orderByRaw(
                            "ts_rank(search_vector, websearch_to_tsquery('russian', ?)) DESC",
                            [$query]
                        )
                        ->take($this->categoryProductsLimit)
                        ->get();
                });

                $category->setRelation('products', $products);
            });

            return $categories;
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
