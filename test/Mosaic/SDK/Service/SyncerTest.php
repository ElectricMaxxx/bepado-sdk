<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\Service;

use Mosaic\Common;
use Mosaic\SDK;

require_once __DIR__ . '/../bootstrap.php';

class SyncerTest extends Common\Test\TestCase
{
    protected $gateway;

    protected function getMySQLiGateway()
    {
        if ($this->gateway) {
            return $this->gateway;
        }

        $config = @parse_ini_file(__DIR__ . '/../../../../build.properties');
        $connection = new \Mosaic\SDK\MySQLi(
            $config['db.hostname'],
            $config['db.userid'],
            $config['db.password'],
            $config['db.name']
        );
        $connection->query('TRUNCATE TABLE mosaic_change;');
        $connection->query('TRUNCATE TABLE mosaic_product;');

        return $this->gateway = new SDK\Gateway\MySQLi($connection);
    }

    /**
     * Get product provider
     *
     * Returns a set of products, as defined by the given array.
     *
     * The "data" can be changed, to cause different product hashes.
     *
     * @param array $products
     * @param string $data
     * @return ProductProvider
     */
    protected function getProductProvider(array $products, $data = 'foo')
    {
        $products = array_map('strval', $products);
        $provider = $this->getMock( '\\Mosaic\\SDK\\ProductProvider' );
        $provider
            ->expects($this->any())
            ->method('getExportedProductIDs')
            ->will($this->returnValue($products));
        $provider
            ->expects($this->any())
            ->method('getProducts')
            ->will($this->returnValue(array_map(
                function ($productId) use ($data)
                {
                    return new SDK\Struct\Product(
                        array(
                            'sourceId' => $productId,
                            'title' => $data,
                        )
                    );
                },
                $products
            )));

        return $provider;
    }

    /**
     * Assert changes are exposed as expected
     *
     * @param array $expectation
     * @param array $changes
     * @return void
     */
    protected function assertChanges($expectation, $changes)
    {
        $this->assertEquals(
            $expectation,
            array_map(
                function ($change)
                {
                    return array($change->operation, $change->sourceId);
                },
                $changes
            )
        );
    }

    public function testInitialBuild()
    {
        $syncer = new SDK\Service\Syncer(
            $gateway = $this->getMySQLiGateway(),
            $this->getProductProvider(array(1, 2)),
            new SDK\RevisionProvider\Time(),
            new SDK\ProductHasher\Simple()
        );
        $syncer->sync();

        $this->assertChanges(
            array(
                array('insert', 1),
                array('insert', 2),
            ),
            $changes = $gateway->getNextChanges(null, 100)
        );
        return end($changes)->revision;
    }

    /**
     * @depends testInitialBuild
     */
    public function testReIndex()
    {
        $revision = $this->testInitialBuild();
        $syncer = new SDK\Service\Syncer(
            $gateway = $this->getMySQLiGateway(),
            $this->getProductProvider(array(1, 2)),
            new SDK\RevisionProvider\Time(),
            new SDK\ProductHasher\Simple()
        );
        $syncer->sync();

        $this->assertChanges(
            array(),
            $gateway->getNextChanges($revision, 100)
        );
    }

    /**
     * @depends testInitialBuild
     */
    public function testProductUpdate()
    {
        $revision = $this->testInitialBuild();
        $syncer = new SDK\Service\Syncer(
            $gateway = $this->getMySQLiGateway(),
            $this->getProductProvider(array(1, 2), 'update'),
            new SDK\RevisionProvider\Time(),
            new SDK\ProductHasher\Simple()
        );
        $syncer->sync();

        $this->assertChanges(
            array(
                array('update', 1),
                array('update', 2),
            ),
            $gateway->getNextChanges($revision, 100)
        );
    }

    /**
     * @depends testInitialBuild
     */
    public function testProductDelete()
    {
        $revision = $this->testInitialBuild();
        $syncer = new SDK\Service\Syncer(
            $gateway = $this->getMySQLiGateway(),
            $this->getProductProvider(array()),
            new SDK\RevisionProvider\Time(),
            new SDK\ProductHasher\Simple()
        );
        $syncer->sync();

        $this->assertChanges(
            array(
                array('delete', 1),
                array('delete', 2),
            ),
            $gateway->getNextChanges($revision, 100)
        );
    }
}
