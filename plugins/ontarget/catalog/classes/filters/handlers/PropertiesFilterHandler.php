<?php

namespace OnTarget\Catalog\Classes\Filters\Handlers;

use October\Rain\Database\Builder;

class PropertiesFilterHandler implements FilterHandler
{
    /**
     * @param Builder $query
     * @param \Closure $next
     * @return Builder
     */
    public function filter(Builder $query, \Closure $next): Builder
    {
        return $next($query);
    }
}
