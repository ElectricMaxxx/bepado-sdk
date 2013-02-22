<?php
/**
 * This file is part of the Bepado SDK Component.
 *
 * @version $Revision$
 */

namespace Bepado\SDK\Gateway;

use Bepado\Common\Test\TestCase;
use Bepado\SDK\Struct\Change\FromShop\Delete;
use Bepado\SDK\Struct\Change\FromShop\Insert;
use Bepado\SDK\Struct\Product;

require_once __DIR__ . '/../bootstrap.php';

/**
 * Common tests for the change gateway implementations.
 */
abstract class ChangeGatewayTest extends TestCase
{
    /**
     * @return void
     */
    public function testGetNextChangesReturnsExpectedNextResult()
    {

        $gateway = $this->createChangeGateway();
        $gateway->recordInsert(
            'avocado-10906',
            md5('avocado-10906'),
            '1358342508.3692500266',
            new Product()
        );
        $gateway->recordDelete(
            'avocado-10906',
            '1358342508.7466800423'
        );

        $this->assertEquals(
            array(
                new Delete(
                    array(
                        'sourceId' => 'avocado-10906',
                        'revision' => '1358342508.7466800423'
                    )
                )
            ),
            $gateway->getNextChanges('1358342508.7466800422', 10)
        );
    }

    /**
     * @return void
     */
    public function testGetNextChangesReturnsNotResultForEqualRevision()
    {

        $gateway = $this->createChangeGateway();
        $gateway->recordInsert(
            'avocado-10906',
            md5('avocado-10906'),
            '1358342508.3692500266',
            new Product()
        );
        $gateway->recordDelete(
            'avocado-10906',
            '1358342508.7466800423'
        );

        $this->assertSame(
            array(),
            $gateway->getNextChanges('1358342508.7466800423', 10)
        );
    }

    /**
     * Factory method which creates our concrete SUT instance.
     *
     * @return \Bepado\SDK\Gateway\ChangeGateway
     */
    abstract protected function createChangeGateway();
}
