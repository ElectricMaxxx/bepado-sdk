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
     * Get product data
     *
     * Get product data for all the product IDs specified in the given string
     * array.
     *
     * @param string[] $ids
     * @return Struct\Product[]
     */
    public function getProducts(array $ids);

    /**
     * Get all IDs of all exported products
     *
     * @return string[]
     */
    public function getExportedProductIDs();
}
