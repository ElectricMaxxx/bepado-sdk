<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\Gateway;

use Mosaic\SDK\Gateway;
use Mosaic\SDK\Struct\Change;
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
class InMemory extends Gateway
{
    protected $products = array();
    protected $changes = array();

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
    public function getNextChanges($offset, $limit)
    {
        $record = $offset === null;
        $changes = array();
        $i = 0;
        foreach ($this->changes as $revision => $data) {
            if ($revision > $offset) {
                $record = true;
            }

            if (!$record ||
                $revision === $offset) {
                unset($this->changes[$revision]);
                continue;
            }

            if ($i >= $limit) {
                break;
            }

            $changes[] = $data;
            $i++;
        }

        return $changes;
    }

    /**
     * Record product insert
     *
     * @param string $id
     * @param string $hash
     * @param string $revision
     * @param Product $product
     * @return void
     */
    public function recordInsert($id, $hash, $revision, Product $product)
    {
        $this->changes[$revision] = new Change\Insert(
            array(
                'sourceId' => $id,
                'revision' => $revision,
                'product'  => $product,
            )
        );
        $this->products[$id] = $hash;
    }

    /**
     * Record product update
     *
     * @param string $id
     * @param string $hash
     * @param string $revision
     * @param Product $product
     * @return void
     */
    public function recordUpdate($id, $hash, $revision, Product $product)
    {
        $this->changes[$revision] = new Change\Update(
            array(
                'sourceId' => $id,
                'revision' => $revision,
                'product'  => $product,
            )
        );
        $this->products[$id] = $hash;
    }

    /**
     * Record product delete
     *
     * @param string $id
     * @param string $revision
     * @return void
     */
    public function recordDelete($id, $revision)
    {
        $this->changes[$revision] = new Change\Delete(
            array(
                'sourceId' => $id,
                'revision' => $revision
            )
        );
        unset($this->products[$id]);
    }

    /**
     * Check if product has changed
     *
     * Return true, if product chenged since last check.
     *
     * @param string $id
     * @param string $hash
     * @return boolean
     */
    public function hasChanged($id, $hash)
    {
        return $this->products[$id] !== $hash;
    }

    /**
     * Get IDs of all recorded products
     *
     * @return string[]
     */
    public function getAllProductIDs()
    {
        return array_keys($this->products);
    }
}
