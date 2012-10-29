<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK;

/**
 * Interface for product providers
 *
 * @version $Revision$
 * @api
 */
interface ProductProvider
{
    /**
     * Get products from shop
     *
     * @param int $offset
     * @param int $limit
     * @return Struct\Product[]
     */
    public function getProducts($offset, $limit);
}
