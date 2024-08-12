<?php

namespace OnTarget\Catalog\Classes\Filters\Handlers;

use Cms\Classes\Controller;
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
            Controller::getController()->getRouter()->getParameter('category')
        );

        if (empty($categoryIdentifier)) return $next($query);

        $category = Category::query()
            ->select('id','slug')
            ->where('slug', $categoryIdentifier)
            ->first();

        $ids = array_merge(
            [$category->id],
            $category->getAllChildrenAndSelf()->pluck('id')->toArray()
        );

        return $next($query->whereIn('category_id', $ids));
    }
}
