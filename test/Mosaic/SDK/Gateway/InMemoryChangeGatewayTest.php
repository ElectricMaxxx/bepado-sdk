<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\Gateway;

/**
 * Test for the in memory change gateway implementation.
 */
class InMemoryChangeGatewayTest extends ChangeGatewayTest
{
    /**
     * Factory method which creates our concrete SUT instance.
     *
     * @return \Mosaic\SDK\Gateway\ChangeGateway
     */
    protected function createChangeGateway()
    {
        return new InMemory();
    }
}
