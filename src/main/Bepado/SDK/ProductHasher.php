<?php
/**
 * This file is part of the Bepado SDK Component.
 *
 * The SDK is licensed under MIT license. (c) Shopware AG and Qafoo GmbH
 */

namespace Bepado\SDK;

/**
 * Base class for product hasher implementations
 *
 * The SDK is licensed under MIT license. (c) Shopware AG and Qafoo GmbH
 */
abstract class ProductHasher
{
    /**
     * Get hash for product
     *
     * @return string
     */
    abstract public function hash(Struct\Product $product);
}
