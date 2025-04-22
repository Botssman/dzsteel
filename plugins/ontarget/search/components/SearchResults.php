<?php namespace OnTarget\Search\Components;

use Cms\Classes\ComponentBase;
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
        $query = input('query');

        if (empty($query)) {
            return;
        }

        $this->page['results'] = $this->service->search($query);
    }
}
