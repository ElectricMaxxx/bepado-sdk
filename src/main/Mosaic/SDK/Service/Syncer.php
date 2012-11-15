<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\Service;

use Mosaic\SDK\Gateway;
use Mosaic\SDK\ProductFromShop;
use Mosaic\SDK\RevisionProvider;
use Mosaic\SDK\ProductHasher;

/**
 * Service to sync product database with changes feed
 *
 * @version $Revision$
 */
class Syncer
{
    /**
     * Gateway to products
     *
     * @var Gateway\ProductGateway
     */
    protected $products;

    /**
     * Gateway to changes feed
     *
     * @var Gateway\ChangeGateway
     */
    protected $changes;

    /**
     * Product from shop
     *
     * @var ProductFromShop
     */
    protected $fromShop;

    /**
     * Revision provider
     *
     * @var RevisionProvider
     */
    protected $revisions;

    /**
     * Product hasher
     *
     * @var ProductHasher
     */
    protected $hasher;

    /**
     * COnstruct from gateway
     *
     * @param Gateway\ProductGateway $gateway
     * @param ProductFromShop $fromShop
     * @param RevisionProvider $revisions
     * @param ProductHasher $hasher
     * @return void
     */
    public function __construct(
        Gateway\ProductGateway $products,
        Gateway\ChangeGateway $changes,
        ProductFromShop $fromShop,
        RevisionProvider $revisions,
        ProductHasher $hasher
    ) {
        $this->products = $products;
        $this->changes = $changes;
        $this->fromShop = $fromShop;
        $this->revisions = $revisions;
        $this->hasher = $hasher;
    }

    /**
     * Sync changes feed with internal database
     *
     * @return void
     */
    public function sync()
    {
        $shopProducts = $this->fromShop->getExportedProductIDs();
        $knownProducts = $this->products->getAllProductIDs();

        if ($deletes = array_diff($knownProducts, $shopProducts)) {
            foreach ($deletes as $productId) {
                $this->changes->recordDelete($productId, $this->revisions->next());
            }
        }

        if ($inserts = array_diff($shopProducts, $knownProducts)) {
            foreach ($this->fromShop->getProducts($inserts) as $product) {
                $this->changes->recordInsert(
                    $product->sourceId,
                    $this->hasher->hash($product),
                    $this->revisions->next(),
                    $product
                );
            }
        }

        if ($toCheck = array_intersect($shopProducts, $knownProducts)) {
            foreach ($this->fromShop->getProducts($toCheck) as $product) {
                if ($this->products->hasChanged(
                    $product->sourceId,
                    $this->hasher->hash($product)
                )) {
                    $this->changes->recordUpdate(
                        $product->sourceId,
                        $this->hasher->hash($product),
                        $this->revisions->next(),
                        $product
                    );
                }
            }
        }
    }
}
