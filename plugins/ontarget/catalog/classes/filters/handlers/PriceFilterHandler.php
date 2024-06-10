<?php

namespace OnTarget\Catalog\Classes\Filters\Handlers;

use Closure;
use October\Rain\Database\Builder;

class PriceFilterHandler implements FilterHandler
{
    /**
     * @param Builder $query
     * @param Closure $next
     * @return Builder
     */
    public function filter(Builder $query, Closure $next): Builder
    {
        $priceMin = request()->filled('price_min') ? str_replace(' ', '', request('price_min')) : null;
        $priceMax = request()->filled('price_max') ? str_replace(' ', '', request('price_max')) : null;

        if ($priceMin && $priceMax) {
            $query->whereBetween('price', [$priceMin, $priceMax]);
        } elseif ($priceMin) {
            $query->where('price', '>=', $priceMin);
        } elseif ($priceMax) {
            $query->where('price', '<=', $priceMax);
        } else {
            return $next($query);
        }

        return $next($query);
    }
}
