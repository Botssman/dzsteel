<?php namespace OnTarget\Catalog\Components;

use Cms\Classes\ComponentBase;
use OnTarget\Catalog\Models\Customer;
use OnTarget\Catalog\Models\Order;

/**
 * Cart Component
 *
 * @link https://docs.octobercms.com/3.x/extend/cms-components.html
 */
class Cart extends ComponentBase
{
    public array $items = [];

    public function componentDetails()
    {
        return [
            'name' => 'Cart Component',
            'description' => 'No description provided yet...'
        ];
    }

    public function init(): void
    {
        $this->setVars();
    }

    public function setVars(): void
    {
        $this->items = \Session::get('cart', []);
        $this->page['cart'] = $this->getCart();
    }

    /**
     * @return array
     */
    public function getCart(): array
    {
        $items = $this->prepareItems($this->items);

        return [
            'total' => count($items),
            'items' => $items,
            'ids' => array_keys($items),
        ];
    }

    /**
     * @param array $items
     * @return mixed
     */
    public function prepareItems(array $items): mixed
    {
        return collect($items)
            ->unique()
            ->mapWithKeys(function ($item) use ($items) {
                return [$item => count(array_keys($items, $item))];
            })
            ->toArray();
    }

    public function addToCart(int $productId): void
    {
        \Session::push('cart', $productId);
        $this->setVars();
    }

    public function removeFromCart(int $productId): void
    {
        $ids = \Session::get('cart');

        $ids = array_filter($ids, fn($i) => $i != $productId);

        \Session::put('cart', $ids);
        $this->setVars();
    }


    public function decrement(int $productId): void
    {
        $ids = \Session::get('cart');

        if (!in_array($productId, $ids)) return;

        $key = array_search($productId, $ids);
        if ($key !== false) {
            unset($ids[$key]);
        }

        \Session::put('cart', $ids);
        $this->setVars();
    }

    public function clearCart(): void
    {
        \Session::put('cart', []);
        $this->setVars();
    }

    public function onAdd()
    {
        $this->addToCart(post('product_id'));
    }

    public function onDecrement()
    {
        $this->decrement(post('product_id'));
    }

    public function onRemove()
    {
        $this->removeFromCart(post('product_id'));
    }

    public function onClear()
    {
        $this->clearCart();
    }

    public function getProducts(array $ids = null)
    {
        return \OnTarget\Catalog\Models\Product::query()
            ->find($ids ?? $this->getCart()['ids']);
    }

    public function getTotalSum(?array $items = null)
    {
        $items = $items ?? $this->getCart()['items'];

        if ($this->getProducts()->isEmpty()) return 0;

        return $this->getProducts()->sum(function (\OnTarget\Catalog\Models\Product $product) use ($items) {
            return $product->price * $items[$product->id];
        });
    }

    public function onCheckout()
    {
        $data = post();

        $customer = Customer::query()
            ->where('phone_number', $data['phone_number'])
            ->firstOrNew();

        $customer->fill($data);
        $customer->save();

        $order = new Order();
        $order->customer_id = $customer->id;
        $order->save();

        $order->products()->sync($this->getCart()['ids']);

        \Session::forget('cart');
    }

}
