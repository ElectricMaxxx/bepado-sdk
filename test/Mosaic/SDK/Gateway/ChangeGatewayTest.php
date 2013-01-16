<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\Gateway;

use Mosaic\Common\Test\TestCase;
use Mosaic\SDK\Struct\Change\FromShop\Delete;
use Mosaic\SDK\Struct\Change\FromShop\Insert;
use Mosaic\SDK\Struct\Product;

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
     * @return \Mosaic\SDK\Gateway\ChangeGateway
     */
    abstract protected function createChangeGateway();
}
