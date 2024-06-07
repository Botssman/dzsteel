<?php

namespace OnTarget\Catalog\Classes\Scopes;

class ActiveScope
{
    public function __invoke(\Illuminate\Database\Eloquent\Builder $builder): void
    {
        $builder->where('is_active', true);
    }
}
