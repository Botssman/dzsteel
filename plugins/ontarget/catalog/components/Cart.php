<?php namespace OnTarget\Catalog\Components;

use Cms\Classes\ComponentBase;

/**
 * Cart Component
 *
 * @link https://docs.octobercms.com/3.x/extend/cms-components.html
 */
class Cart extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name' => 'Cart Component',
            'description' => 'No description provided yet...'
        ];
    }

    /**
     * @link https://docs.octobercms.com/3.x/element/inspector-types.html
     */
    public function defineProperties()
    {
        return [];
    }
}
