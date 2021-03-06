<?php
/**
 * This file is part of the Bepado SDK Component.
 *
 * The SDK is licensed under MIT license. (c) Shopware AG and Qafoo GmbH
 */

namespace Bepado\SDK\Service;

use Bepado\SDK\Gateway;
use Bepado\SDK\ProductToShop;
use Bepado\SDK\ProductFromShop;
use Bepado\SDK\Struct\Change;
use Bepado\SDK\Struct;
use Bepado\SDK\Gateway\ChangeGateway;
use Bepado\SDK\Gateway\RevisionGateway;
use Bepado\SDK\Gateway\ShopConfiguration;

/**
 * Product service
 *
 * The SDK is licensed under MIT license. (c) Shopware AG and Qafoo GmbH
 */
class ProductService
{
    /**
     * Gateway to changes feed
     *
     * @var \Bepado\SDK\Gateway\ChangeGateway
     */
    protected $changes;

    /**
     * Gateway to revision storage
     *
     * @var \Bepado\SDK\Gateway\RevisionGateway
     */
    protected $revision;

    /**
     * Gateway for shop configurations
     *
     * @var \Bepado\SDK\Gateway\ShopConfiguration
     */
    protected $configurationGateway;

    /**
     * Product importer
     *
     * @var \Bepado\SDK\ProductToShop
     */
    protected $toShop;

    /**
     * @var \Bepado\SDK\ProductFromShop
     */
    protected $fromShop;

    /**
     * Construct from gateway
     *
     * @param ChangeGateway $changes
     * @param RevisionGateway $revision
     * @param ShopConfiguration $configurationGateway
     * @param ProductToShop $toShop
     * @param ProductFromShop $fromShop
     * @return void
     */
    public function __construct(
        ChangeGateway $changes,
        RevisionGateway $revision,
        ShopConfiguration $configurationGateway,
        ProductToShop $toShop,
        ProductFromShop $fromShop
    ) {
        $this->changes = $changes;
        $this->revision = $revision;
        $this->configurationGateway = $configurationGateway;
        $this->toShop = $toShop;
        $this->fromShop = $fromShop;
    }

    /**
     * @deprecated Use the getChanges() method directly
     */
    public function fromShop($revision, $productCount)
    {
        return $this->getChanges($revision, $productCount);
    }

    /**
     * Export current change state to Bepado
     *
     * @param string $since
     * @param int $limit
     * @return \Bepado\SDK\Struct\Change[]
     */
    public function getChanges($since, $limit)
    {
        $changes = $this->changes->getNextChanges($since, $limit);

        $this->changes->cleanChangesUntil($since);

        return $changes;
    }

    /**
     * Take a look at products from given revision.
     *
     * @param string $revision
     * @param int $productCount
     * @return \Bepado\SDK\Struct\Change[]
     */
    public function peakFromShop($revision, $productCount)
    {
        return $this->changes->getNextChanges($revision, $productCount);
    }

    /**
     * Return all products matching the given ids.
     *
     * @param array<string> $ids
     * @return \Bepado\SDK\Struct\Product[]
     */
    public function peakProducts(array $ids)
    {
        if (count($ids) > 50) {
            throw new \InvalidArgumentException("Too many products requested.");
        }

        return $this->fromShop->getProducts($ids);
    }

    /**
     * @deprecated Just use the replicate() method directly
     */
    public function toShop(array $changes)
    {
        return $this->replicate($changes);
    }

    /**
     * Import changes into shop
     *
     * @param \Bepado\SDK\Struct\Change[] $changes
     * @return string
     */
    public function replicate(array $changes)
    {
        $this->toShop->startTransaction();
        foreach ($changes as $change) {
            switch (true) {
                case $change instanceof Change\ToShop\InsertOrUpdate:
                    $this->toShop->insertOrUpdate($change->product);
                    break;
                case $change instanceof Change\ToShop\Delete:
                    $this->toShop->delete($change->shopId, $change->sourceId);
                    break;
                default:
                    throw new \RuntimeException("Invalid change operation: $change");
            }
        }

        $this->revision->storeLastRevision($change->revision);

        $this->toShop->commit();
        return $change->revision;
    }

    /**
     * @deprecated Just use the lastRevision() method directly
     */
    public function getLastRevision()
    {
        return $this->lastRevision();
    }

    /**
     * Get last processed revision in shop
     *
     * @return string
     */
    public function lastRevision()
    {
        return $this->revision->getLastRevision();
    }
}
