<?php namespace OnTarget\Search;

use Backend;
use OnTarget\Search\Components\SearchForm;
use OnTarget\Search\Components\SearchResults;
use System\Classes\PluginBase;

/**
 * Plugin Information File
 *
 * @link https://docs.octobercms.com/3.x/extend/system/plugins.html
 */
class Plugin extends PluginBase
{
    /**
     * pluginDetails about this plugin.
     */
    public function pluginDetails()
    {
        return [
            'name' => 'Search',
            'description' => 'No description provided yet...',
            'author' => 'OnTarget',
            'icon' => 'icon-leaf'
        ];
    }

    /**
     * register method, called when the plugin is first registered.
     */
    public function register()
    {
        //
    }

    /**
     * boot method, called right before the request route.
     */
    public function boot()
    {
        //
    }

    /**
     * registerComponents used by the frontend.
     */
    public function registerComponents()
    {
        return [
            SearchResults::class => 'SearchResults',
            SearchForm::class => 'SearchForm',
        ];
    }

    /**
     * registerPermissions used by the backend.
     */
    public function registerPermissions()
    {
        return []; // Remove this line to activate

        return [
            'ontarget.search.some_permission' => [
                'tab' => 'Search',
                'label' => 'Some permission'
            ],
        ];
    }

    /**
     * registerNavigation used by the backend.
     */
    public function registerNavigation()
    {
        return []; // Remove this line to activate

        return [
            'search' => [
                'label' => 'Search',
                'url' => Backend::url('ontarget/search/mycontroller'),
                'icon' => 'icon-leaf',
                'permissions' => ['ontarget.search.*'],
                'order' => 500,
            ],
        ];
    }
}
