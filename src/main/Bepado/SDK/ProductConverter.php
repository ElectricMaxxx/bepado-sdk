<?php
/**
 * This file is part of the Bepado SDK Component.
 *
 * The SDK is licensed under MIT license. (c) Shopware AG and Qafoo GmbH
 */

namespace Bepado\SDK;

use Bepado\SDK\Struct\Product;

/**
 * For various tasks in combination with the SDK you will need a way to convert the product datastructures of
 * your shop system to Bepado product datastructures.
 *
 * So implement this interface to for that conversion.
 *
 * @author Maximilian Berghoff <Maximilian.Berghoff@gmx.de>
 */
interface ProductConverter
{
    /**
     * Creates a bepado product from a given local product implementation.
     *
     * @param $shopProduct
     *
     * @return Product
     */
    public function toBepadoProduct($shopProduct);

    /**
     * Creates the local product implementation from a given bepado product.
     *
     * @param Product $bepadoProduct
     *
     * @return object
     */
    public function toShopProduct(Product $bepadoProduct);
}
 