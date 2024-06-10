<?php

namespace OnTarget\Catalog\Classes\Filters\Handlers;

use October\Rain\Database\Builder;
use OnTarget\Catalog\Classes\Scopes\ActiveScope;
use OnTarget\Catalog\Models\Property;
use OnTarget\Catalog\Models\PropertyValue;

class PropertiesFilterHandler implements FilterHandler
{
    /**
     * @param Builder $query
     * @param \Closure $next
     * @return Builder
     */
    public function filter(Builder $query, \Closure $next): Builder
    {
        $properties = Property::query()
            ->tap(fn() => new ActiveScope)
            ->whereIn('slug', array_keys((array)input()))
            ->get();

        foreach ($properties as $property) {
            $input = input($property->slug);
            if (empty($input)) continue;

            $query->whereHas('property_values', function ($q) use ($property, $input) {
                $q->where('property_id', $property->id)
                    ->whereIn(
                        'slug',
                        (array)$input
                    );
            });

        }

        return $next($query);
    }
}
