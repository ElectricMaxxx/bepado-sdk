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
     * Verify integrity of order
     *
     * Throws a \RuntimeException if the array does not fulfill all
     * requirements.
     *
     * @return void
     */
    public function verify()
    {
        if (!is_int($this->count) ||
            $this->count <= 0) {
            throw new \RuntimeException('Count MUST be a positive integer.');
        }

        if (!$this->product instanceof Product) {
            throw new \RuntimeException('Product MUST be an instance of \\Mosaic\\SDK\\Struct\\Product.');
        }
        $this->product->verify();
    }
}
