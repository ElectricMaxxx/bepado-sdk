<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\Struct\Change\ToShop;

use Mosaic\SDK\Struct\Change;
use Mosaic\SDK\Struct\Product;

/**
 * Insert change struct
 *
 * @version $Revision$
 * @api
 */
class InsertOrUpdate extends Change
{
    /**
     * Operation type
     *
     * @var Product
     */
    public $product;
}
