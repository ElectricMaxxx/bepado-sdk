<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\Service;

use Mosaic\SDK\Gateway;
use Mosaic\SDK\ProductProvider;
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
     * Gateway to changes feed
     *
     * @var Gateway
     */
    protected $gateway;

    /**
     * Product provider
     *
     * @var ProductProvider
     */
    protected $products;

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
     * @param Gateway $gateway
     * @return void
     */
    public function __construct(
        Gateway $gateway,
        ProductProvider $products,
        RevisionProvider $revisions,
        ProductHasher $hasher
    ) {
        $this->gateway = $gateway;
        $this->products = $products;
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
        $shopProducts = $this->products->getExportedProductIDs();
        $knownProducts = $this->gateway->getAllProductIDs();

        if ($deletes = array_diff($knownProducts, $shopProducts)) {
            foreach ($deletes as $productId) {
                $this->gateway->recordDelete($productId, $this->revisions->next());
            }
        }

        if ($inserts = array_diff($shopProducts, $knownProducts)) {
            foreach ($this->products->getProducts($inserts) as $product) {
                $this->gateway->recordInsert(
                    $product->sourceId,
                    $this->hasher->hash($product),
                    $this->revisions->next(),
                    $product
                );
            }
        }

        if ($toCheck = array_intersect($shopProducts, $knownProducts)) {
            foreach ($this->products->getProducts($toCheck) as $product) {
                if ($this->gateway->hasChanged(
                    $product->sourceId,
                    $this->hasher->hash($product)
                )) {
                    $this->gateway->recordUpdate(
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
