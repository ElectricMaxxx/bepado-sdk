<?php
/**
 * This file is part of the Bepado SDK Component.
 *
 * @version $Revision$
 */

namespace Bepado\SDK\Gateway;

/**
 * Test for the in memory change gateway implementation.
 */
class InMemoryChangeGatewayTest extends ChangeGatewayTest
{
    /**
     * Factory method which creates our concrete SUT instance.
     *
     * @return \Bepado\SDK\Gateway\ChangeGateway
     */
    protected function createChangeGateway()
    {
        return new InMemory();
    }
}
