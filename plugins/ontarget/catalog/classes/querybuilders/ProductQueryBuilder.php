<?php

namespace OnTarget\Catalog\Classes\QueryBuilders;

use October\Rain\Database\Builder;
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
}
