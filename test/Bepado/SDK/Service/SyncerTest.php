<?php
/**
 * This file is part of the Bepado SDK Component.
 *
 * @version $Revision$
 */

namespace Bepado\SDK\Service;

use Bepado\Common;
use Bepado\SDK\Struct\RpcCall;
use Bepado\SDK;
use Bepado\SDK\Struct\Change\FromShop\Delete;
use Bepado\SDK\Struct\Change\FromShop\Insert;
use Bepado\SDK\Struct\Change\FromShop\Update;
use Bepado\SDK\HttpClient\NoSecurityRequestSigner;

require_once __DIR__ . '/../bootstrap.php';

abstract class SyncerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Bepado\SDK\SDK
     */
    protected $sdk;

    /**
     * Dependency Resolver
     *
     * @var DependencyResolver
     */
    protected $dependencies;

    /**
     * Get used gateway for test
     *
     * @return SDK\Gateway
     */
    abstract protected function getGateway();

    /**
     * Get SDK
     *
     * @param SDK\ProductFromShop $productFromShop
     * @return SDK
     */
    protected function getSDK(SDK\ProductFromShop $productFromShop)
    {
        $gateway = $this->getGateway();
        $gateway->setShopId('shop');
        $gateway->setCategories(
            array(
                '/others' => 'Others',
            )
        );

        $this->sdk = new SDK\SDK(
            'apikey',
            'http://example.com/endpoint',
            $gateway,
            $this->getMock('\\Bepado\\SDK\\ProductToShop'),
            $productFromShop,
            null,
            new NoSecurityRequestSigner()
        );

        $dependenciesProperty = new \ReflectionProperty($this->sdk, 'dependencies');
        $dependenciesProperty->setAccessible(true);
        $this->dependencies = $dependenciesProperty->getValue($this->sdk);

        return $this->sdk;
    }

    /**
     * Make a RPC call using the marshalling and unmarshalling
     *
     * @param RpcCall $rpcCall
     * @return mixed
     */
    protected function makeRpcCall(RpcCall $rpcCall)
    {
        $result = $this->dependencies->getUnmarshaller()->unmarshal(
            $this->sdk->handle(
                $this->dependencies->getMarshaller()->marshal($rpcCall)
            )
        );

        return $result->arguments[0]->result;
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
     * @return \Bepado\SDK\ProductFromShop
     */
    protected function getProductFromShop(array $products, $data = 'foo')
    {
        $products = array_map('strval', $products);
        $provider = $this->getMock('\\Bepado\\SDK\\ProductFromShop');
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
                            return SyncerTest::getProduct($productId, $data);
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
     * @return \Bepado\SDK\Struct\Product
     */
    public static function getProduct($productId, $data = 'foo')
    {
        return new SDK\Struct\Product(
            array(
                'shopId' => 'shop-1',
                'sourceId' => (string) $productId,
                'title' => $data,
                'price' => $productId * .89,
                'purchasePrice' => $productId * .89,
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
                    $this->dependencies->getVerificator()->verify($change);

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
            $changes = $this->makeRpcCall(
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
            $this->makeRpcCall(
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
            $this->makeRpcCall(
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

        $this->makeRpcCall(
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
            $this->makeRpcCall(
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
            $this->makeRpcCall(
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
