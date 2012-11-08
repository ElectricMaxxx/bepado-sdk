<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK;

/**
 * Shop gateway base class
 *
 * Gateway to interact with other shops
 *
 * @version $Revision$
 */
abstract class ShopGateway
{
    /**
     * Reserve order in remote shop
     *
     * Products SHOULD be reserved and not be sold out while bing reserved.
     * Reservation may be cancelled after sufficient time has passed.
     *
     * Returns true on success, or an array of Struct\Change with updates for
     * the requested products.
     *
     * @param Struct\Order
     * @return mixed
     */
    abstract public function checkProducts(Struct\Order $order);

    /**
     * Reserve order in remote shop
     *
     * Products SHOULD be reserved and not be sold out while bing reserved.
     * Reservation may be cancelled after sufficient time has passed.
     *
     * Returns a reservationId on success, or an array of Struct\Change with
     * updates for the requested products.
     *
     * @param Struct\Order
     * @return mixed
     */
    abstract public function reserve(Struct\Order $order);

    /**
     * Buy order associated with reservation in the remote shop.
     *
     * Returns true on success, or a Struct\Message on failure. SHOULD never
     * fail.
     *
     * @param string $reservationId
     * @return mixed
     */
    abstract public function buy($reservationId);

    /**
     * Confirm a reservation in the remote shop.
     *
     * Returns true on success, or a Struct\Message on failure. SHOULD never
     * fail.
     *
     * @param string $reservationId
     * @return mixed
     */
    abstract public function confirm($reservationId);
}
