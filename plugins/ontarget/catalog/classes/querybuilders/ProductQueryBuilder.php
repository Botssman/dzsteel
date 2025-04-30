<?php

namespace OnTarget\Catalog\Classes\QueryBuilders;

use October\Rain\Database\Builder;
use Ontarget\Catalog\Classes\Enums\Sort;
use OnTarget\Catalog\Classes\Filters\FilteringPipeline;

class ProductQueryBuilder extends Builder
{
    /**
     * @return $this
     */
    public function applyFilters() : static
    {
        return FilteringPipeline::filter($this);
    }

    /**
     * @return ProductQueryBuilder
     */
    public function sort() : static
    {
        return static::orderBy(...Sort::from(input('sort', 'new'))->data());
    }

    /**
     * @param string $term
     * @return $this
     */
    public function search(string $term): static
    {
        return $this->whereRaw("
        search_vector @@ websearch_to_tsquery('russian', ?)
    ", [$term])
            ->orderByRaw("
        ts_rank(search_vector, websearch_to_tsquery('russian', ?)) DESC
    ", [$term]);
    }
}
