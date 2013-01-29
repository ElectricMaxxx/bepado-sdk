<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\Service;

use Mosaic\Common;
use Mosaic\Common\Struct\RpcCall;
use Mosaic\SDK;
use Mosaic\SDK\Struct\Change\FromShop\Delete;
use Mosaic\SDK\Struct\Change\FromShop\Insert;
use Mosaic\SDK\Struct\Change\FromShop\Update;

require_once __DIR__ . '/../bootstrap.php';

abstract class SyncerTest extends Common\Test\TestCase
{
    /**
     * @var \Mosaic\SDK\SDK
     */
    protected $sdk;

    /**
     * Get used gateway for test
     *
     * @return SDK\Gateway
     */
    abstract protected function getGateway();

    protected function getSDK(SDK\ProductFromShop $productFromShop)
    {
        $gateway = $this->getGateway();
        $gateway->setShopId('shop');
        $gateway->setCategories(
            array(
                '/others' => 'Others',
            )
        );

        return $this->sdk = new SDK\SDK(
            'apikey',
            'http://example.com/endpoint',
            $gateway,
            $this->getMock('\\Mosaic\\SDK\\ProductToShop'),
            $productFromShop
        );
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
     * @return \Mosaic\SDK\ProductFromShop
     */
    protected function getProductFromShop(array $products, $data = 'foo')
    {
        $products = array_map('strval', $products);
        $provider = $this->getMock('\\Mosaic\\SDK\\ProductFromShop');
        $provider
            ->expects($this->any())
            ->method('getExportedProductIDs')
            ->will($this->returnValue($products));
        $provider
            ->expects($this->any())
            ->method('getProducts')
            ->will(
                $this->returnValue(
                    array_map(
                        function ($productId) use ($data) {
                            return $this->getProduct($productId, $data);
                        },
                        $products
                    )
                )
            );

        return $provider;
    }

    /**
     * Get fake product for ID
     *
     * @param int $productId
     * @param string $data
     * @return \Mosaic\SDK\Struct\Product
     */
    protected function getProduct($productId, $data = 'foo')
    {
        return new SDK\Struct\Product(
            array(
                'shopId' => 'shop-1',
                'sourceId' => (string) $productId,
                'title' => $data,
                'price' => $productId * .89,
                'currency' => 'EUR',
                'availability' => $productId,
                'categories' => array('/others'),
            )
        );
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
                function ($change) {
                    $this->sdk->getVerificator()->verify($change);

                    // We do not care to comapre revision and product in change
                    $change = clone $change;
                    $change->revision = null;
                    if (isset($change->product)) {
                        $change->product = null;
                    }
                    return $change;
                },
                $changes
            )
        );
    }

    public function testInitialBuild()
    {
        $sdk = $this->getSdk($this->getProductFromShop(array(1, 2)));
        $sdk->recreateChangesFeed();

        $this->assertChanges(
            array(
                new Insert(array('sourceId' => '1')),
                new Insert(array('sourceId' => '2')),
            ),
            $changes = $this->sdk->getServiceRegistry()->dispatch(
                new RpcCall(
                    array(
                        'service' => 'products',
                        'command' => 'fromShop',
                        'arguments' => array(null, 100),
                    )
                )
            )
        );
        return end($changes)->revision;
    }

    /**
     * @depends testInitialBuild
     */
    public function testReIndex()
    {
        $revision = $this->testInitialBuild();
        $sdk = $this->getSdk($this->getProductFromShop(array(1, 2)));
        $sdk->recreateChangesFeed();

        $this->assertChanges(
            array(),
            $this->sdk->getServiceRegistry()->dispatch(
                new RpcCall(
                    array(
                        'service' => 'products',
                        'command' => 'fromShop',
                        'arguments' => array($revision, 100),
                    )
                )
            )
        );
    }

    /**
     * @depends testReIndex
     */
    public function testProductUpdate()
    {
        $revision = $this->testInitialBuild();
        $sdk = $this->getSdk($this->getProductFromShop(array(1, 2), 'update'));
        $sdk->recreateChangesFeed();

        $this->assertChanges(
            array(
                new Update(array('sourceId' => '1')),
                new Update(array('sourceId' => '2')),
            ),
            $this->sdk->getServiceRegistry()->dispatch(
                new RpcCall(
                    array(
                        'service' => 'products',
                        'command' => 'fromShop',
                        'arguments' => array($revision, 100),
                    )
                )
            )
        );
    }

    /**
     * @depends testProductUpdate
     */
    public function testReFetchChanges()
    {
        $revision = $this->testInitialBuild();
        $sdk = $this->getSdk($this->getProductFromShop(array(1, 2), 'update'));
        $sdk->recreateChangesFeed();

        $this->sdk->getServiceRegistry()->dispatch(
            new RpcCall(
                array(
                    'service' => 'products',
                    'command' => 'fromShop',
                    'arguments' => array($revision, 100),
                )
            )
        );

        $this->assertChanges(
            array(
                new Update(array('sourceId' => '1')),
                new Update(array('sourceId' => '2')),
            ),
            $this->sdk->getServiceRegistry()->dispatch(
                new RpcCall(
                    array(
                        'service' => 'products',
                        'command' => 'fromShop',
                        'arguments' => array($revision, 100),
                    )
                )
            )
        );
    }

    /**
     * @depends testReIndex
     */
    public function testProductDelete()
    {
        $revision = $this->testInitialBuild();
        $sdk = $this->getSdk($this->getProductFromShop(array()));
        $sdk->recreateChangesFeed();

        $this->assertChanges(
            array(
                new Delete(array('sourceId' => '1')),
                new Delete(array('sourceId' => '2')),
            ),
            $this->sdk->getServiceRegistry()->dispatch(
                new RpcCall(
                    array(
                        'service' => 'products',
                        'command' => 'fromShop',
                        'arguments' => array($revision, 100),
                    )
                )
            )
        );
    }
}
