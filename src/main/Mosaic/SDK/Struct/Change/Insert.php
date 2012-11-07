<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\Struct\Change;

use Mosaic\SDK\Struct\Change;
use Mosaic\SDK\Struct\Product;

/**
 * Insert change struct
 *
 * @version $Revision$
 * @api
 */
class Insert extends Change
{
    /**
     * Operation type
     *
     * @var Product
     */
    public $product;

    /**
     * Verify struct integrity
     *
     * Throws a RuntimeException if integrity is not given.
     *
     * @return void
     */
    public function verify()
    {
        parent::verify();

        if (!$this->product instanceof Product) {
            throw new \RuntimeException('Property $product must be a Struct\Product.');
        }
        $this->product->verify();
    }
}
