<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\Struct\Change\InterShop;

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
     * Updated product
     *
     * @var Product
     */
    public $product;

    /**
     * Old version of Product
     *
     * @var Product
     */
    public $oldProduct;
}
