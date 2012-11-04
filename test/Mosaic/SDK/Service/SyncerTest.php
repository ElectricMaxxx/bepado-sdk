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
    protected function getMySQLiGateway()
    {
        $config = @parse_ini_file(__DIR__ . '/../../../../build.properties');
        $connection = new \Mosaic\SDK\MySQLi(
            $config['db.hostname'],
            $config['db.userid'],
            $config['db.password'],
            $config['db.name']
        );
        $connection->query('TRUNCATE TABLE mosaic_change;');
        $connection->query('TRUNCATE TABLE mosaic_product;');

        return new SDK\Gateway\MySQLi($connection);
    }

    protected function getProductProvider(array $products)
    {
        $provider = $this->getMock( '\\Mosaic\\SDK\\ProductProvider' );
        $provider
            ->expects($this->at(0))
            ->method('getExportedProductIDs')
            ->will($this->returnValue($products));
        $provider
            ->expects($this->at(1))
            ->method('getProducts')
            ->with($products)
            ->will($this->returnValue(array_map(
                function ($productId)
                {
                    return new SDK\Struct\Product(
                        array(
                            'sourceId' => $productId,
                        )
                    );
                },
                $products
            )));

        return $provider;
    }

    public function testInitialBuild()
    {
        $syncer = new \Mosaic\SDK\Service\Syncer(
            $this->getMySQLiGateway(),
            $this->getProductProvider(array(1, 2))
        );

        $syncer->sync();
    }
}
