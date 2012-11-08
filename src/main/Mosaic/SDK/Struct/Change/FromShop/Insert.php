<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\Struct\Change\FromShop;

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
     * New product
     *
     * @var Product
     */
    public $product;
}
