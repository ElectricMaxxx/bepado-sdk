<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\Service;

use Mosaic\SDK\Gateway;
use Mosaic\SDK\ProductToShop;
use Mosaic\SDK\Struct\Change;

/**
 * Product service
 *
 * @version $Revision$
 */
class Product
{
    /**
     * Gateway to changes feed
     *
     * @var Gateway
     */
    protected $gateway;

    /**
     * Product importer
     *
     * @var ProductToShop
     */
    protected $toShop;

    /**
     * COnstruct from gateway
     *
     * @param Gateway $gateway
     * @return void
     */
    public function __construct(Gateway $gateway, ProductToShop $toShop)
    {
        $this->gateway = $gateway;
        $this->toShop = $toShop;
    }

    /**
     * Export current change state to Mosaic
     *
     * @param string $revision
     * @param int $productCount
     * @return Struct\Change[]
     */
    public function fromShop($revision, $productCount)
    {
        return $this->gateway->getNextChanges($revision, $productCount);
    }

    /**
     * Import changes into shop
     *
     * @param Change[] $changes
     * @return string
     */
    public function toShop(array $changes)
    {
        foreach ($changes as $change) {
            switch (true) {
                case $change instanceof Change\ToShop\InsertOrUpdate:
                    $this->toShop->insertOrUpdate($change->product);
                    continue 2;
                case $change instanceof Change\ToShop\Delete:
                    $this->toShop->delete($change->shopId, $change->sourceId);
                    continue 2;
                default:
                    throw new \RuntimeException("Invalid change operation: $change");
            }
        }

        $this->gateway->storeLastRevision($change->revision);
        return $change->revision;
    }

    /**
     * Get last processed revision in shop
     *
     * @return string
     */
    public function getLastRevision()
    {
        return $this->gateway->getLastRevision();
    }
}
