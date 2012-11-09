<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\Service;

use Mosaic\SDK\Gateway;
use Mosaic\SDK\Struct;

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

    /**
     * Check order in shop
     *
     * Verifies, if all products in the given order still have the same price
     * and availability.
     *
     * Returns true on success, or an array of Struct\Change with updates for
     * the requested products.
     *
     * @param Struct\Product[] $products
     * @return mixed
     */
    public function checkProducts(array $products)
    {
        // @TODO: Actually verify with shop
        return true;
    }

    /**
     * Reserve order in shop
     *
     * Products SHOULD be reserved and not be sold out while bing reserved.
     * Reservation may be cancelled after sufficient time has passed.
     *
     * Returns a reservationId on success, or an array of Struct\Change with
     * updates for the requested products.
     *
     * @param Struct\Product[] $products
     * @return mixed
     */
    public function reserveProducts(array $products)
    {
        // @TODO: Actually reserve products
        return 'foo';
    }

    /**
     * Buy order associated with reservation in the remote shop.
     *
     * Returns true on success, or a Struct\Message on failure. SHOULD never
     * fail.
     *
     * @param string $reservationId
     * @return mixed
     */
    public function buy($reservationId)
    {
        // @TODO: Buy products
        return true;
    }

    /**
     * Confirm a reservation in the remote shop.
     *
     * Returns true on success, or a Struct\Message on failure. SHOULD never
     * fail.
     *
     * @param string $reservationId
     * @return mixed
     */
    public function confirm($reservationId)
    {
        // @TODO: Confirm buy and log transaction
        return true;
    }
}
