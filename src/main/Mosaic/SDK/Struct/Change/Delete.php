<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\Struct\Change;

use Mosaic\SDK\Struct\Change;

/**
 * Delete change struct
 *
 * @version $Revision$
 * @api
 */
class Delete extends Change
{
    /**
     * Shop ID
     *
     * @var string
     */
    public $shopId;
}
