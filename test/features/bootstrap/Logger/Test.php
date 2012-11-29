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
class Test extends Logger
{
    /**
     * Log messages
     *
     * @var array
     */
    protected $logMessages = array();

    /**
     * Log order
     *
     * @param Struct\Order $order
     * @return void
     */
    public function log(Struct\Order $order)
    {
        $this->logMessages[] = $order;
    }

    /**
     * Get log messages occured during test run
     *
     * @return array
     */
    public function getLogMessages()
    {
        return $this->logMessages;
    }
}
