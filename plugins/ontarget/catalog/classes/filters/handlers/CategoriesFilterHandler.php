<?php

namespace OnTarget\Catalog\Classes\Filters\Handlers;

use October\Rain\Database\Builder;
use OnTarget\Catalog\Models\Category;
use Route;

class CategoriesFilterHandler implements FilterHandler
{
    /**
     * @param Builder $query
     * @param \Closure $next
     * @return Builder
     */
    public function filter(Builder $query, \Closure $next): Builder
    {
        $categoryIdentifier = request(
            'category',
            Route::current()
                ->parameter('slug')
        );

        if (empty($categoryIdentifier)) return $next($query);

        $ids = Category::query()
            ->select('id', 'slug')
            ->where('id', $categoryIdentifier)
            ->orWhere('slug', $categoryIdentifier)
            ->getAllChildrenAndSelf()
            ->pluck('id');

        return $next($query->whereIn('category_id', $ids));
    }
}
