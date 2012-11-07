<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\Service;

use Mosaic\SDK\Gateway;
use Mosaic\SDK\ProductImporter;
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
     * @var ProductImporter
     */
    protected $importer;

    /**
     * COnstruct from gateway
     *
     * @param Gateway $gateway
     * @return void
     */
    public function __construct(Gateway $gateway, ProductImporter $importer)
    {
        $this->gateway = $gateway;
        $this->importer = $importer;
    }

    /**
     * Export current change state to Mosaic
     *
     * @param string $revision
     * @param int $productCount
     * @return Struct\Change[]
     */
    public function export($revision, $productCount)
    {
        return $this->gateway->getNextChanges($revision, $productCount);
    }

    /**
     * Import changes into shop
     *
     * @param Change[] $changes
     * @return string
     */
    public function import(array $changes)
    {
        foreach ($changes as $change) {
            switch (true) {
                case $change instanceof Change\ToShop\InsertOrUpdate:
                    $this->importer->insertOrUpdate($change->product);
                    continue 2;
                case $change instanceof Change\ToShop\Delete:
                    $this->importer->delete($change->shopId, $change->sourceId);
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
