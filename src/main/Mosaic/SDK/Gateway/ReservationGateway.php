<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\Gateway;

use Mosaic\SDK\Struct;

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
     * @param Struct\Order $order
     * @return string
     */
    public function createReservation(Struct\Order $order);

    /**
     * Get order for reservation ID
     *
     * @param string $reservationID
     * @return Struct\Order
     */
    public function getOrder($reservationID);

    /**
     * Set reservation as bought
     *
     * @param string $reservationID
     * @param Struct\Order $order
     * @return void
     */
    public function setBought($reservationID, Struct\Order $order);

    /**
     * Set reservation as confirmed
     *
     * @param string $reservationID
     * @return void
     */
    public function setConfirmed($reservationID);
}
