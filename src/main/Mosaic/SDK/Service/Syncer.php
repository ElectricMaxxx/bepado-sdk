<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\Service;

use Mosaic\SDK\Gateway;
use Mosaic\SDK\ProductProvider;

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
    protected $provider;

    /**
     * COnstruct from gateway
     *
     * @param Gateway $gateway
     * @return void
     */
    public function __construct(Gateway $gateway, ProductProvider $provider)
    {
        $this->gateway = $gateway;
        $this->provider = $provider;
    }

    /**
     * Sync changes feed with internal database
     *
     * @return void
     */
    public function sync()
    {
        $shopProducts = $this->provider->getExportedProductIDs();
        $knownProducts = $this->gateway->getAllProductIDs();

        $deletes = array_diff($knownProducts, $shopProducts);
        foreach ($deletes as $productId) {
            $this->gateway->recordInsert($productId);
        }

        $inserts = array_diff($shopProducts, $knownProducts);
        foreach ($this->provider->getProducts($inserts) as $product) {
            $this->gateway->recordInsert($product);
        }

        $toCheck = array_intersect($shopProducts, $knownProducts);
        foreach ($this->provider->getProducts($toCheck) as $product) {
            if ($this->gateway->hasChanged($product))
            {
                $this->gateway->recordUpdate($product);
            }
        }
    }
}
