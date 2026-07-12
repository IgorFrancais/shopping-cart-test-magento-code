<?php

namespace FortNine\ShoppingCartTest\Api\Data;

interface ConfigInterface
{
    public const string MSG_ITEM_ADD_OK = 'Product added to cart.';
    public const string MSG_UNKNOWN_SKU = 'Unknown product SKU.';
    public const string MSG_CART_CLEARED = 'Cart cleared.';
    public const string MSG_CART_UPDATED = 'Cart updated.';

    public const string URL_CART_ADD = 'shoppingcarttest/cart/add';
    public const string URL_CART_UPDATE = 'shoppingcarttest/cart/update';
    public const string URL_CART_INDEX = 'shoppingcarttest/cart/index';

    public const TABLE = 'fortnine_cart_item';

    public const GST_RATE = 0.05;
    public const QST_RATE = 0.09975;

    public const CATALOG = [
        'HELMET-001' => ['name' => 'Trail Helmet', 'price' => 79.99],
        'GLOVES-001' => ['name' => 'All-Terrain Gloves', 'price' => 32.50],
        'PACK-001' => ['name' => 'Hydration Pack', 'price' => 54.25],
        'LIGHT-001' => ['name' => 'LED Headlight', 'price' => 24.75],
    ];
}
