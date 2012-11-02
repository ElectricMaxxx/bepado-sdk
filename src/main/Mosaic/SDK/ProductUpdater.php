<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK;

/**
 * Interface for product updaters
 *
 * Implement this interface with shop specific details to update products in
 * your shop database, which originate from mosaic.
 *
 * @version $Revision$
 * @api
 */
interface ProductUpdater
{
    /**
     * Update Product
     *
     * @param Struct\Product[]
     * @return void
     */
    public function updateProduct(Struct\Product $product);
}
