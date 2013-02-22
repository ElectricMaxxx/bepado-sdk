<?php
/**
 * This file is part of the Bepado SDK Component.
 *
 * @version $Revision$
 */

namespace Bepado\SDK\Logger;

use Bepado\SDK\Logger;
use Bepado\SDK\Struct;

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
    protected function doLog(Struct\Order $order)
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
