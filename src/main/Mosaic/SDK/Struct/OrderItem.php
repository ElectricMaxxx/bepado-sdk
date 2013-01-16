<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\Struct;

use Mosaic\SDK\Struct;

/**
 * Struct class representing an order item
 *
 * @version $Revision$
 * @api
 */
class OrderItem extends Struct
{
    /**
     * @var int
     */
    public $count;

    /**
     * @var Product
     */
    public $product;

    /**
     * Restores an order item from a previously stored state array.
     *
     * @param array $state
     * @return \Mosaic\SDK\Struct\OrderItem
     */
    public static function __set_state(array $state)
    {
        return new OrderItem($state);
    }
}
