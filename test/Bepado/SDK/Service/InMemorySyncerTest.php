<?php
/**
 * This file is part of the Bepado SDK Component.
 *
 * @version $Revision$
 */

namespace Bepado\SDK\Service;

use Bepado\Common;
use Bepado\SDK;

require_once __DIR__ . '/SyncerTest.php';

class InMemorySyncerTest extends SyncerTest
{
    protected $gateway;

    /**
     * Get used gateway for test
     *
     * @return SDK\Gateway
     */
    protected function getGateway()
    {
        if ($this->gateway) {
            return $this->gateway;
        }

        return $this->gateway = new SDK\Gateway\InMemory();
    }
}
