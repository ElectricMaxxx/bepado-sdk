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
     * Returns the reservation Id
     *
     * @param Struct\Order $order
     * @return string
     */
    public function createReservation(Struct\Order $order);

    /**
     * Get order for reservation Id
     *
     * @param string $reservationId
     * @return Struct\Order
     */
    public function getOrder($reservationId);

    /**
     * Set reservation as bought
     *
     * @param string $reservationId
     * @param Struct\Order $order
     * @return void
     */
    public function setBought($reservationId, Struct\Order $order);

    /**
     * Set reservation as confirmed
     *
     * @param string $reservationId
     * @return void
     */
    public function setConfirmed($reservationId);
}
