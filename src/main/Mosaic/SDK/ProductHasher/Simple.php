<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\ProductHasher;

use Mosaic\SDK\ProductHasher;
use Mosaic\SDK\Struct;

/**
 * Base class for product hasher implementations
 *
 * @version $Revision$
 */
class Simple extends ProductHasher
{
    /**
     * Get hash for product
     *
     * @param \Mosaic\SDK\Struct\Product $product
     * @return string
     */
    public function hash(Struct\Product $product)
    {
        return md5(serialize($product));
    }
}
