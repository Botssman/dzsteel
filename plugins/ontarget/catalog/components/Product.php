<?php namespace OnTarget\Catalog\Components;

use Cms\Classes\ComponentBase;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use OnTarget\Catalog\Classes\Scopes\ActiveScope;
use Response;

/**
 * Product Component
 *
 * @link https://docs.octobercms.com/3.x/extend/cms-components.html
 */
class Product extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name' => 'Product Component',
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

    public function onRun()
    {
        try {
            $this->page['product'] = \OnTarget\Catalog\Models\Product::query()
                ->tap(fn() => new ActiveScope)
                ->whereHas('category', function ($q){
                    return $q->where('slug', $this->param('category'));
                })
                ->where('slug', $this->param('product'))
                ->firstOrFail();
        } catch (ModelNotFoundException $exception) {
            return Response::make($this->controller->run('404')->getContent(), 404);
        }
    }
}
