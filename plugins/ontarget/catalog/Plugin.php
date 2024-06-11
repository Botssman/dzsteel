<?php namespace OnTarget\Catalog;

use Backend;
use Event;
use OnTarget\Catalog\Classes\Search\CatalogSearchProvider;
use OnTarget\Catalog\Components\Cart;
use OnTarget\Catalog\Components\Catalog;
use OnTarget\Catalog\FormWidgets\Properties;
use OnTarget\Catalog\Models\CatalogSettings;
use OnTarget\Catalog\Models\Product;
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
        Event::listen('offline.sitesearch.extend', function () {
            return new CatalogSearchProvider;
        });
    }

    /**
     * registerComponents used by the frontend.
     */
    public function registerComponents()
    {
        return [
            Catalog::class => 'Catalog',
            Product::class => 'Product',
            Cart::class => 'Cart',
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
            'ontarget.catalog.properties' => [
                'tab' => 'Каталог',
                'label' => 'Управление свойствами'
            ],
            'ontarget.catalog.orders' => [
                'tab' => 'Каталог',
                'label' => 'Управление заказами'
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
                    ],
                    'properties' => [
                        'label' => 'Характеристики',
                        'url' => Backend::url('ontarget/catalog/properties'),
                        'icon' => 'ph ph-check',
                        'permissions' => ['ontarget.catalog.properties'],
                        'order' => 500,
                    ],
                    'measureunits' => [
                        'label' => 'Единицы измерения',
                        'url' => Backend::url('ontarget/catalog/measureunits'),
                        'icon' => 'ph ph-scales',
                        'permissions' => ['ontarget.catalog.measureunits'],
                        'order' => 500,
                    ],
                    'orders' => [
                        'label' => 'Заказы',
                        'url' => Backend::url('ontarget/catalog/orders'),
                        'icon' => 'ph ph-shopping-cart',
                        'permissions' => ['ontarget.catalog.orders'],
                        'order' => 500,
                    ]
                ]
            ],
        ];
    }

    public function registerFormWidgets()
    {
        return [
            Properties::class => 'properties'
        ];
    }

    public function registerMarkupTags(): array
    {
        return [
            'filters' => [
                'price' => [$this, 'formatPrice'],
                'plural'  => [$this, 'getPlural'],
            ]
        ];
    }

    public function registerSettings()
    {
        return [
            'catalog-settings' => [
                'label' => 'Настройки каталога',
                'description' => 'Управление настройками каталога и магазина',
                'category' => 'Каталог',
                'icon' => 'icon-shopping-cart',
                'class' => CatalogSettings::class
            ]
        ];
    }

    /**
     * @param float|null $price
     * @return string
     */
    public function formatPrice(?float $price): string
    {
        if (empty($price)) return '';
        return number_format($price, 0, '', ' ');
    }

    /**
     * @param int|null $number
     * @param array $params
     * @return string
     */
    public function getPlural(?int $number, array $params) : string
    {
        if (empty($number)) return '';

        $cases = [2, 0, 1, 1, 1, 2];
        return $number . ' ' . $params[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]];
    }
}
