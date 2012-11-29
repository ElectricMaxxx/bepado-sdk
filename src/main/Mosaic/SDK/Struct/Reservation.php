<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\Struct;

use Mosaic\SDK\Struct;

/**
 * Struct class representing a multi-shop reservation
 *
 * @version $Revision$
 * @api
 */
class Reservation extends Struct
{
    /**
     * Messages from shops, where the reservation failed.
     *
     * @var array
     */
    public $messages = array();

    /**
     * Orders per shop
     *
     * @var Struct\Order[]
     */
    public $orders = array();
}
