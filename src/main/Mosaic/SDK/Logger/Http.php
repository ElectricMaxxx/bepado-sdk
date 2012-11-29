<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\Logger;

use Mosaic\SDK\Logger;
use Mosaic\SDK\Struct;

/**
 * Base class for logger implementations
 *
 * @version $Revision$
 */
class Http extends Logger
{
    /**
     * Log order
     *
     * @param Struct\Order $order
     * @return void
     */
    public function log(Struct\Order $order)
    {
        throw new \RuntimeException('@TODO: Implement');
    }
}
