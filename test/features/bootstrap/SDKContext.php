<?php

namespace Bepado\SDK;

use Behat\Behat\Context\BehatContext;

use Bepado\Common\Rpc;
use Bepado\Common\Struct;
use Bepado\SDK\Struct\Product;

require_once __DIR__ . '/ShopGateway/DirectAccess.php';
require_once __DIR__ . '/ShopFactory/DirectAccess.php';
require_once __DIR__ . '/Logger/Test.php';

/**
 * Base SDK features context.
 */
class SDKContext extends BehatContext
{
    /**
     * SDK entry point
     *
     * @var SDK
     */
    protected $sdk;

    /**
     * SDK dependencies for optional direct access
     *
     * @var DependencyResolver
     */
    protected $dependencies;

    /**
     * Currently used mock for to shop gateway
     *
     * @var ProductToShop
     */
    protected $productToShop;

    /**
     * Currently used mock for from shop gateway
     *
     * @var ProductFromShop
     */
    protected $productFromShop;

    /**
     * Main gateway of the local shop
     *
     * @var Gateway
     */
    protected $gateway;

    protected function getGateway($connection)
    {
        $storage = getenv('STORAGE') ?: 'InMemory';
        switch ($storage) {
            case 'InMemory':
                $gateway = new Gateway\InMemory();
                break;
            case 'MySQLi':
                $gateway = new Gateway\MySQLi($connection);
                break;
            case 'PDO':
                $gateway = new Gateway\PDO($connection);
                break;
            default:
                throw new \RuntimeException("Unknown storage backend $storage");
        }

        $gateway->setShopId('shop');
        $gateway->setCategories(
            array(
                '/others' => 'Others',
            )
        );
        $gateway->setEnabledFeatures(array('shipping_rules'));
        $gateway->setCategoriesLastRevision("");

        return $gateway;
    }

    public function initSDK($connection)
    {
        $this->productToShop = \Phake::mock('\\Bepado\\SDK\\ProductToShop');
        $this->productFromShop = \Phake::partialMock(
            '\\Bepado\\SDK\\ProductFromShop\\Test'
        );

        $this->sdk = new SDK(
            'apikey',
            'http://example.com/endpoint',
            $this->gateway = $this->getGateway($connection),
            $this->productToShop,
            $this->productFromShop,
            null,
            new \Bepado\SDK\HttpClient\NoSecurityRequestSigner()
        );

        $dependenciesProperty = new \ReflectionProperty($this->sdk, 'dependencies');
        $dependenciesProperty->setAccessible(true);
        $this->dependencies = $dependenciesProperty->getValue($this->sdk);
    }

    /**
     * Get fake product for ID
     *
     * @param int $productId
     * @return Product
     */
    protected function getProduct($productId, $data = 'foo')
    {
        return new Product(
            array(
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
     * Make a RPC call using the marshalling and unmarshalling
     *
     * @param Struct\RpcCall $rpcCall
     * @return mixed
     */
    protected function makeRpcCall(Struct\RpcCall $rpcCall)
    {
        $result = $this->dependencies->getUnmarshaller()->unmarshal(
            $this->sdk->handle(
                $this->dependencies->getMarshaller()->marshal($rpcCall)
            )
        );

        return $result->arguments[0]->result;
    }
}
