<?php namespace OnTarget\Catalog\Classes\Filters\Handlers;

use October\Rain\Database\Builder;

interface FilterHandler
{
    /**
     * @param Builder $query
     * @param \Closure $next
     * @return Builder
     */
    public function filter(Builder $query, \Closure $next) : Builder;
}
