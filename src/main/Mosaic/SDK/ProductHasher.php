<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK;

/**
 * Base class for product hasher implementations
 *
 * @version $Revision$
 * @api
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
