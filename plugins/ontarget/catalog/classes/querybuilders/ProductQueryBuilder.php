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

    public function search()
    {

    }
}
