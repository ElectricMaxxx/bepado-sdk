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
 * Service to maintain transactions
 *
 * @version $Revision$
 */
class Transaction
{
    /**
     * Product gateway
     *
     * @var Gateway\Products
     */
    protected $products;

    /**
     * COnstruct from gateway
     *
     * @param Gateway\Products $gateway
     * @param ProductFromShop $fromShop
     * @param RevisionProvider $revisions
     * @param ProductHasher $hasher
     * @return void
     */
    public function __construct(
        Gateway\Products $products
    ) {
        $this->products = $products;
    }
}
