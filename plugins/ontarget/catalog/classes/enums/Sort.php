<?php namespace Ontarget\Catalog\Classes\Enums;

enum Sort : string
{
    case NEW = 'new';
    case OLD = 'old';
    case CHEAP = 'cheap';
    case EXPENSIVE = 'expensive';

    /**
     * @return string[]
     */
    public function data() : array
    {
        return match($this) {
            self::NEW       => ['created_at', 'desc'],
            self::OLD       => ['created_at', 'asc'],
            self::CHEAP     => ['price', 'asc'],
            self::EXPENSIVE => ['price', 'desc'],
        };
    }
}
