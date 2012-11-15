<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\Gateway;

use Mosaic\SDK\Struct\Product;

/**
 * Gateway interface to maintain product hashes and exported products
 *
 * @version $Revision$
 * @api
 */
interface ProductGateway
{
    /**
     * Check if product has changed
     *
     * Return true, if product chenged since last check.
     *
     * @param string $id
     * @param string $hash
     * @return boolean
     */
    public function hasChanged($id, $hash);

    /**
     * Get IDs of all recorded products
     *
     * @return string[]
     */
    public function getAllProductIDs();
}
