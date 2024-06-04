<?php namespace OnTarget\Catalog;

use Backend;
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
            'name' => 'Каталог',
            'description' => 'Каталог товаров',
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

        ];
    }

    /**
     * registerPermissions used by the backend.
     */
    public function registerPermissions()
    {
        return [
            'ontarget.catalog.products' => [
                'tab' => 'Каталог',
                'label' => 'Управление товарами'
            ],
            'ontarget.catalog.categories' => [
                'tab' => 'Каталог',
                'label' => 'Управление категориями'
            ],
        ];
    }

    /**
     * registerNavigation used by the backend.
     */
    public function registerNavigation()
    {
        return [
            'catalog' => [
                'label' => 'Каталог',
                'url' => Backend::url('ontarget/catalog/categories'),
                'icon' => 'ph ph-list',
                'permissions' => ['ontarget.catalog.categories'],
                'order' => 500,
                'sideMenu' => [
                    'categories' => [
                        'label' => 'Категории',
                        'url' => Backend::url('ontarget/catalog/categories'),
                        'icon' => 'ph ph-list',
                        'permissions' => ['ontarget.catalog.categories'],
                        'order' => 500,
                    ],
                    'products' => [
                        'label' => 'Товары',
                        'url' => Backend::url('ontarget/catalog/products'),
                        'icon' => 'ph ph-barcode',
                        'permissions' => ['ontarget.catalog.products'],
                        'order' => 500,
                    ]
                ]
            ],
        ];
    }
}
