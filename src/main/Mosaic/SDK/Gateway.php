<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK;

use Mosaic\SdkApi\Struct\Product;

/**
 * Abstract base class to store SDK related data
 *
 * You may create custom extensions of this class, if the default data stores
 * do not work for you.
 *
 * @version $Revision$
 * @api
 */
abstract class Gateway
{
    /**
     * Get next changes
     *
     * @param int $limit
     * @return Struct\Changes[]
     */
    abstract public function getNextChanges($limit);

    /**
     * Record product insert
     *
     * @param Struct\Product $product
     * @return void
     */
    abstract public function recordInsert(Product $product);

    /**
     * Record product update
     *
     * @param Struct\Product $product
     * @return void
     */
    abstract public function recordUpdate(Product $product);

    /**
     * Record product delete
     *
     * @param Struct\Product $product
     * @return void
     */
    abstract public function recordDelete(Product $product);
}
