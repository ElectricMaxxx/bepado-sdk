<?php
/**
 * This file is part of the Bepado SDK Component.
 *
 * The SDK is licensed under MIT license. (c) Shopware AG and Qafoo GmbH
 */

namespace Bepado\SDK\ProductFromShop;

use Bepado\SDK\ProductFromShop;
use Bepado\SDK\Struct;

/**
 * Interface for product providers
 *
 * The SDK is licensed under MIT license. (c) Shopware AG and Qafoo GmbH
 * @api
 */
class Test implements ProductFromShop
{
    /**
     * Get product data
     *
     * Get product data for all the product IDs specified in the given string
     * array.
     *
     * @param string[] $ids
     * @return Struct\Product[]
     */
    public function getProducts(array $ids)
    {
        return array_map(
            function($productId) {
                return new Struct\Product(
                    array(
                        'sourceId' => (string) $productId,
                        'title' => 'Sindelfingen ' . microtime(),
                        'price' => $productId * .89,
                        'purchasePrice' => $productId * .89,
                        'currency' => 'EUR',
                        'availability' => $productId,
                        'categories' => array('/others'),
                    )
                );
            },
            $ids
        );
    }

    /**
     * Get all IDs of all exported products
     *
     * @return string[]
     */
    public function getExportedProductIDs()
    {
        return array();
    }

    /**
     * Reserve a product in shop for purchase
     *
     * @param Struct\Order $order
     * @return void
     * @throws \Exception Abort reservation by throwing an exception here.
     */
    public function reserve(Struct\Order $order)
    {
        // Nothing
    }

    /**
     * Buy products mentioned in order
     *
     * Should return the internal order ID.
     *
     * @param Struct\Order $order
     * @return string
     *
     * @throws \Exception Abort buy by throwing an exception,
     *                    but only in very important cases.
     *                    Do validation in {@see reserve} instead.
     */
    public function buy(Struct\Order $order)
    {
        // Nothing
    }
}
