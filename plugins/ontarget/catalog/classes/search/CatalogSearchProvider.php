<?php namespace OnTarget\Catalog\Classes\Search;

use Cms\Classes\Page;
use OFFLINE\SiteSearch\Classes\Providers\ResultsProvider;
use OnTarget\Catalog\Models\Product;

class CatalogSearchProvider extends ResultsProvider
{

    public function search()
    {
        $matching = Product::query()
            ->where('name', 'like', "%{$this->query}%")
            ->orWhere('slug', 'like', "%{$this->query}%")
            ->orWhere('description', 'like', "%{$this->query}%")
            ->orWhere('vendor_code', $this->query)
            ->cursorPaginate();

        foreach ($matching as $match) {
            $result            = $this->newResult();

            $result->relevance = 1;
            $result->title     = $match->name;
            $result->text      = $match->description;
            $result->url       = Page::url('catalog/product', ['slug' => $match->slug]);
            $result->thumb     = $match->image ?? $match->images->first();
            $result->model     = $match;
//            $result->meta      = [
//                'some_data' => $match->some_other_property,
//            ];

            // Add the results to the results collection
            $this->addResult($result);
        }

        return $this;
    }

    public function displayName()
    {
        return 'My Result';
    }

    public function identifier()
    {
        return 'VendorName.PluginName';
    }
}
