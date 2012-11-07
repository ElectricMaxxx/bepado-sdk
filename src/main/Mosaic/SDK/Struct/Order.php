<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\Struct;

use Mosaic\SDK\Struct;

/**
 * Struct class representing an order
 *
 * @version $Revision$
 * @api
 */
class Order extends Struct
{
    /**
     * @var string
     */
    public $reservationID;

    /**
     * @var float
     */
    public $shippingCosts;

    /**
     * @var OrderItem[]
     */
    public $products;
}
