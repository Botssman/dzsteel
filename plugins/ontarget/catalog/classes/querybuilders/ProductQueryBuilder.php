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
        return $this
            ->selectRaw("ontarget_catalog_products.*,
            ts_rank(search_vector, websearch_to_tsquery('russian', ?)) as relevance,
            COALESCE(ontarget_catalog_products.rank, 0) as priority",
                [$term])
            ->whereRaw("search_vector @@ websearch_to_tsquery('russian', ?)", [$term])
            ->orderByRaw('relevance * priority DESC, relevance DESC');    }
}
