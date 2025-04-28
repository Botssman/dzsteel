<?php namespace OnTarget\Search\Components;

use Cms\Classes\ComponentBase;
use OnTarget\Catalog\Models\Category;
use OnTarget\Search\Classes\Services\SearchService;

/**
 * SearchResults Component
 *
 * @link https://docs.octobercms.com/3.x/extend/cms-components.html
 */
class SearchResults extends ComponentBase
{
    public SearchService $service;

    public function componentDetails()
    {
        return [
            'name' => 'Search Results Component',
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

    public function init()
    {
        $this->service = new SearchService();
    }

    public function onRun()
    {
        $this->setVars();
    }

    public function onProductListingUpdate()
    {
        $this->setVars();
    }

    public function setVars()
    {
        $query = input('query');

        if (empty($query)) {
            return;
        }

        $category = Category::query()
            ->where('slug', $this->param('category'))
            ->first();

        if ($category) {
            $this->page['category'] = $category;
            $this->page['products'] = $this->service->searchByCategory($query, $category->id);
        } else {
            $this->page['categories'] = $this->service->search($query);
        }
    }

    public function onQuickSearch()
    {
        $query = input('query');

        if (!empty($query)) {
            $this->page['quickSearchResults'] = $this->service->quickSearch($query);
        } else {
            $this->page['quickSearchResults'] = null;
        }

    }
}
