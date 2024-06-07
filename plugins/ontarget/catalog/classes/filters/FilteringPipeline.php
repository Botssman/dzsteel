<?php

namespace OnTarget\Catalog\Classes\Filters;

use Illuminate\Pipeline\Pipeline;
use October\Rain\Database\Builder;
use OnTarget\Catalog\Classes\Filters\Handlers\PriceFilterHandler;
use OnTarget\Catalog\Classes\Filters\Handlers\PropertiesFilterHandler;

class FilteringPipeline
{
    /**
     * @var array|\class-string[]
     */
    public static array $handlers = [
        PriceFilterHandler::class,
        PropertiesFilterHandler::class
    ];

    /**
     * @param Builder $query
     * @return mixed
     */
    public static function filter(Builder $query)
    {
        return app(Pipeline::class)
            ->send($query)
            ->through(self::$handlers)
            ->via('filter')
            ->then(fn($query) => $query);
    }
}
