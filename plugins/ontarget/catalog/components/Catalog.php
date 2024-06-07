<?php namespace OnTarget\Catalog\Components;

use Cms\Classes\ComponentBase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use October\Rain\Database\Collection;
use OnTarget\Catalog\Classes\Scopes\ActiveScope;
use OnTarget\Catalog\Models\Category;
use \OnTarget\Catalog\Models\Product as ProductModel;
use Response;

/**
 * Catalog Component
 *
 * @link https://docs.octobercms.com/3.x/extend/cms-components.html
 */
class Catalog extends ComponentBase
{
    /**
     * @var Category|null
     */
    public Category|null $currentCategory;

    /**
     * @var Collection|null
     */
    public Collection|null $categories;

    public function componentDetails()
    {
        return [
            'name' => 'Catalog Component',
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
        /**
         * Looking for /catalog/:category at first
         */
        if (!empty($this->param('category'))) {
            try {
                $this->setVars('currentCategory', $this->getCurrentCategory());
            } catch (ModelNotFoundException $exception) {
                return $this->notFound();
            }

            $this->setVars('categories', $this->currentCategory->children);
        }

        if (empty($this->categories)) {
            $this->setVars('categories', $this->getRootCategories());
        }

        $this->page['products'] = $this->getProducts();
    }

    /**
     * @return Category
     */
    public function getCurrentCategory(): Category
    {
        return Category::query()
            ->tap(fn() => new ActiveScope)
            ->where('slug', $this->param('category'))
            ->firstOrFail();
    }

    public function getRootCategories()
    {
        return Category::query()
            ->tap(fn() => new ActiveScope)
            ->getAllRoot();
    }

    public function getProducts()
    {
        return ProductModel::query()
            ->tap(fn() => new ActiveScope)
            ->applyFilters()
            ->cursorPaginate();
    }

    /**
     * @param string $key
     * @param string $value
     * @return void
     */
    public function setVars(string $key, mixed $value): void
    {
        $this->$key = $this->page[$key] = $value;
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function notFound(): \Illuminate\Http\Response
    {
        return Response::make($this->controller->run('404')->getContent(), 404);

    }
}
