<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\Gateway;

use Mosaic\SDK\Gateway;
use Mosaic\SDK\Struct;
use Mosaic\SDK\Struct\Order;
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
    protected $lastRevision;
    protected $shopConfiguration = array();
    protected $shopId = null;
    protected $lastVerificationDate = null;
    protected $categories = array();
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
     * @return \Mosaic\SDK\Struct\Change[]
     */
    public function getNextChanges($offset, $limit)
    {
        $record = $offset === null;
        $changes = array();
        $i = 0;
        foreach ($this->changes as $revision => $data) {
            if (strcmp($revision, $offset) > 0) {
                $record = true;
            }

            if (!$record || $revision === $offset) {
                unset($this->changes[$revision]);
                continue;
            }

            if ($i >= $limit) {
                break;
            }

            $changes[] = $this->createChange($data);
            $i++;
        }

        return $changes;
    }

    /**
     * Get unprocessed changes count
     *
     * The offset specified the revision to start from
     *
     * @param string $offset
     * @param int $limit
     * @return Struct\Changes[]
     */
    public function getUnprocessedChangesCount($offset, $limit)
    {
        $record = $offset === null;
        $changes = array();
        $i = 0;
        $count = 0;
        foreach ($this->changes as $revision => $data) {
            if (strcmp($revision, $offset) > 0) {
                $record = true;
            }

            if (!$record || $revision === $offset) {
                continue;
            }

            if ($i >= $limit) {
                $count++;
            }

            $i++;
        }

        return $count;
    }

    private function createChange(array $data)
    {
        switch ($data['type']) {
            case 'delete':
                $class = '\\Mosaic\\SDK\\Struct\\Change\\FromShop\\Delete';
                break;
            case 'insert':
                $class = '\\Mosaic\\SDK\\Struct\\Change\\FromShop\\Insert';
                break;
            case 'update':
                $class = '\\Mosaic\\SDK\\Struct\\Change\\FromShop\\Update';
                break;
        }

        unset($data['type']);

        return new $class($data);
    }

    /**
     * Record product insert
     *
     * @param string $id
     * @param string $hash
     * @param string $revision
     * @param \Mosaic\SDK\Struct\Product $product
     * @return void
     */
    public function recordInsert($id, $hash, $revision, Product $product)
    {
        $this->checkRevisionExists($revision);

        $this->changes[$revision] = array(
            'type'     => 'insert',
            'sourceId' => $id,
            'revision' => $revision,
            'product'  => $product
        );
        $this->products[$id] = $hash;
    }

    /**
     * Record product update
     *
     * @param string $id
     * @param string $hash
     * @param string $revision
     * @param \Mosaic\SDK\Struct\Product $product
     * @return void
     */
    public function recordUpdate($id, $hash, $revision, Product $product)
    {
        $this->checkRevisionExists($revision);

        $this->changes[$revision] = array(
            'type'     => 'update',
            'sourceId' => $id,
            'revision' => $revision,
            'product'  => $product
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
        $this->checkRevisionExists($revision);

        $this->changes[$revision] = array(
            'type'     => 'delete',
            'sourceId' => $id,
            'revision' => $revision
        );
        unset($this->products[$id]);
    }

    /**
     * @param string $revision
     * @throws \InvalidArgumentException
     */
    private function checkRevisionExists($revision)
    {
        if (isset($this->changes[$revision])) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Revision %s already exists for shop %s.',
                    $revision,
                    $this->shopId
                )
            );
        }
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
     * @param \Mosaic\SDK\Struct\ShopConfiguration $configuration
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
     * @return \Mosaic\SDK\Struct\ShopConfiguration
     */
    public function getShopConfiguration($shopId)
    {
        return $this->shopConfiguration[$shopId];
    }

    /**
     * Set category mapping
     *
     * @param array $categories
     * @return void
     */
    public function setCategories(array $categories)
    {
        $this->categories = $categories;
    }

    /**
     * Get category mapping
     *
     * @return array
     */
    public function getCategories()
    {
        return $this->categories;
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
        $this->lastVerificationDate = time();
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
     * Get last shop verification date as Unix UTC timestamp
     *
     * @return int
     */
    public function getLastVerificationDate()
    {
        return $this->lastVerificationDate;
    }

    /**
     * Create and store reservation
     *
     * Returns the reservation Id
     *
     * @param \Mosaic\SDK\Struct\Order $order
     * @return string
     */
    public function createReservation(Order $order)
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
     * @return \Mosaic\SDK\Struct\Order
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
     * @param \Mosaic\SDK\Struct\Order $order
     * @return void
     */
    public function setBought($reservationId, Order $order)
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

    /**
     * Restores an in-memory gateway from a previously stored state array.
     *
     * @param array $state
     * @return \Mosaic\SDK\Gateway\InMemory
     */
    public static function __set_state(array $state)
    {
        $gateway = new InMemory();
        foreach ($state as $name => $value) {
            $gateway->$name = $value;
        }
        return $gateway;
    }
}
