<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK;

/**
 * Base class for logger implementations
 *
 * @version $Revision$
 */
abstract class Logger
{
    /**
     * Log order
     *
     * @param Struct\Order $order
     * @return void
     */
    abstract public function log(Struct\Order $order);
}
