<?php

namespace Mosaic\SDK;

use Behat\Behat\Context\BehatContext;

use Mosaic\Common\Rpc;
use Mosaic\Common\Struct;
use Mosaic\SDK\Struct\Product;

use \PHPUnit_Framework_MockObject_Generator as Mocker;

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

    public function __construct()
    {
        $this->initSDK();
    }

    protected function getGateway()
    {
        $storage = getenv('STORAGE') ?: 'InMemory';
        switch ($storage) {
            case 'InMemory':
                $gateway = new Gateway\InMemory();
                break;
            case 'MySQLi':
                $config = @parse_ini_file(__DIR__ . '/../../../build.properties');
                $gateway = new Gateway\MySQLi(
                    $connection = new MySQLi(
                        $config['db.hostname'],
                        $config['db.userid'],
                        $config['db.password'],
                        $config['db.name']
                    )
                );
                $connection->query('TRUNCATE TABLE mosaic_change;');
                $connection->query('TRUNCATE TABLE mosaic_product;');
                $connection->query('TRUNCATE TABLE mosaic_data;');
                $connection->query('TRUNCATE TABLE mosaic_reservations;');
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
        return $gateway;
    }

    protected function initSDK()
    {
        $productToShop = Mocker::getMock('\\Mosaic\\SDK\\ProductToShop');
        $productFromShop = Mocker::getMock('\\Mosaic\\SDK\\ProductFromShop');

        $this->sdk = new SDK(
            'apikey',
            'http://example.com/endpoint',
            $this->getGateway(),
            $productToShop,
            $productFromShop
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
                // shopId is maintained by the SDK
                // 'shopId' => 'shop-1',
                'sourceId' => (string) $productId,
                'title' => $data,
                'price' => $productId * .89,
                'currency' => 'EUR',
                'availability' => $productId,
                'categories' => array('/others'),
            )
        );
    }

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
