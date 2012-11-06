<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\Struct\Change;

use Mosaic\SDK\Struct\Change;

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
}