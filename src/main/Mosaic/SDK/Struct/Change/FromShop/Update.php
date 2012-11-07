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
 * Update change struct
 *
 * @version $Revision$
 * @api
 */
class Update extends Change
{
    /**
     * Operation type
     *
     * @var Product
     */
    public $product;
}
