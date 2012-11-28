<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\Gateway;

use Mosaic\SDK\Struct\Product;

/**
 * Gateway interface to maintain product hashes and exported products
 *
 * @version $Revision$
 * @api
 */
interface ReservationGateway
{
    /**
     * Create and store reservation
     *
     * Returns the reservation ID
     *
     * @param Struct\OrderItem[] $orders
     * @return string
     */
    public function createReservation(array $orders);
}
