<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\Gateway;

use Mosaic\SDK\Gateway;
use Mosaic\SDK\Struct;

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
    protected $lastRevision;
    protected $shopConfiguration = array();
    protected $shopId = null;
    protected $reservations = array();

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
     * @param Struct\Product $product
     * @return void
     */
    public function recordInsert($id, $hash, $revision, Struct\Product $product)
    {
        $this->changes[$revision] = new Struct\Change\FromShop\Insert(
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
     * @param Struct\Product $product
     * @return void
     */
    public function recordUpdate($id, $hash, $revision, Struct\Product $product)
    {
        $this->changes[$revision] = new Struct\Change\FromShop\Update(
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
        $this->changes[$revision] = new Struct\Change\FromShop\Delete(
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
     * Get Ids of all recorded products
     *
     * @return string[]
     */
    public function getAllProductIds()
    {
        return array_keys($this->products);
    }

    /**
     * Get last processed import revision
     *
     * @return string
     */
    public function getLastRevision()
    {
        return $this->lastRevision;
    }

    /**
     * Store last processed import revision
     *
     * @param string $revision
     * @return void
     */
    public function storeLastRevision($revision)
    {
        $this->lastRevision = $revision;
    }

    /**
     * Update shop configuration
     *
     * @param string $shopId
     * @param Struct\ShopConfiguration $configuration
     * @return void
     */
    public function setShopConfiguration($shopId, Struct\ShopConfiguration $configuration)
    {
        $this->shopConfiguration[$shopId] = $configuration;
    }

    /**
     * Get configuration for the given shop
     *
     * @param string $shopId
     * @return Struct\ShopConfiguration
     */
    public function getShopConfiguration($shopId)
    {
        return $this->shopConfiguration[$shopId];
    }

    /**
     * Set own shop ID
     *
     * @param string $shopId
     * @return void
     */
    public function setShopId($shopId)
    {
        $this->shopId = $shopId;
    }

    /**
     * Get own shop ID
     *
     * @return string
     */
    public function getShopId()
    {
        return $this->shopId ?: false;
    }

    /**
     * Create and store reservation
     *
     * Returns the reservation Id
     *
     * @param Struct\Order $order
     * @return string
     */
    public function createReservation(Struct\Order $order)
    {
        $reservationId = md5(microtime());
        $this->reservations[$reservationId] = array(
            'order' => $order,
            'state' => 'new',
        );

        return $reservationId;
    }

    /**
     * Get order for reservation Id
     *
     * @param string $reservationId
     * @return Struct\Order
     */
    public function getOrder($reservationId)
    {
        if (!isset($this->reservations[$reservationId])) {
            throw new \RuntimeException("Unknown reservation $reservationId");
        }

        return $this->reservations[$reservationId]['order'];
    }

    /**
     * Set reservation as bought
     *
     * @param string $reservationId
     * @param Struct\Order $order
     * @return void
     */
    public function setBought($reservationId, Struct\Order $order)
    {
        if (!isset($this->reservations[$reservationId])) {
            throw new \RuntimeException("Unknown reservation $reservationId");
        }

        $this->reservations[$reservationId]['order'] = $order;
        $this->reservations[$reservationId]['state'] = 'bought';
    }

    /**
     * Set reservation as confirmed
     *
     * @param string $reservationId
     * @return void
     */
    public function setConfirmed($reservationId)
    {
        if (!isset($this->reservations[$reservationId])) {
            throw new \RuntimeException("Unknown reservation $reservationId");
        }

        $this->reservations[$reservationId]['state'] = 'confirmed';
    }
}
