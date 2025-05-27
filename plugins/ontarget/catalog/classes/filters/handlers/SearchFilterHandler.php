<?php

namespace OnTarget\classes\filters\handlers;

use Closure;
use October\Rain\Database\Builder;
use OnTarget\Catalog\Classes\Filters\Handlers\FilterHandler;

class SearchFilterHandler implements FilterHandler
{
    /**
     * @param Builder $query
     * @param Closure $next
     * @return Builder
     */
    public function filter(Builder $query, Closure $next): Builder
    {
        $searchQuery = request('search');

        if (!empty($searchQuery)){
            $query->search($searchQuery);
        }

        return $next($query);
    }
}
