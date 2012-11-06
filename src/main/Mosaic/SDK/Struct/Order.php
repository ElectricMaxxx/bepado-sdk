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

    /**
     * Verify integrity of order
     *
     * Throws a \RuntimeException if the array does not fulfill all
     * requirements.
     *
     * @return void
     */
    public function verify()
    {
        if (!is_array($this->products)) {
            throw new \RuntimeException('Products MUST be an array.');
        }

        foreach ($this->products as $product) {
            if (!$product instanceof OrderItem) {
                throw new \RuntimeException('Products array MUST contain only instances of \\Mosaic\\SDK\\Struct\\OrderItem.');
            }

            $product->verify();
        }
    }
}
