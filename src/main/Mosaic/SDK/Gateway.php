<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK;

use Mosaic\SDK\Struct\Product;

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
     * The offset specified the revision to start from
     *
     * May remove all pending changes, which are prior to the last requested
     * revision.
     *
     * @param string $offset
     * @param int $limit
     * @return Struct\Changes[]
     */
    abstract public function getNextChanges($offset, $limit);

    /**
     * Record product insert
     *
     * @param string $id
     * @param string $hash
     * @param string $revision
     * @param Product $product
     * @return void
     */
    abstract public function recordInsert($id, $hash, $revision, Product $product);

    /**
     * Record product update
     *
     * @param string $id
     * @param string $hash
     * @param string $revision
     * @param Product $product
     * @return void
     */
    abstract public function recordUpdate($id, $hash, $revision, Product $product);

    /**
     * Record product delete
     *
     * @param string $id
     * @param string $revision
     * @return void
     */
    abstract public function recordDelete($id, $revision);

    /**
     * Check if product has changed
     *
     * Return true, if product chenged since last check.
     *
     * @param string $id
     * @param string $hash
     * @return boolean
     */
    abstract public function hasChanged($id, $hash);

    /**
     * Get IDs of all recorded products
     *
     * @return string[]
     */
    abstract public function getAllProductIDs();
}
