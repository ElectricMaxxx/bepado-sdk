<?php

namespace Mosaic\SDK;

use Behat\Behat\Context\BehatContext;

use Mosaic\Common\Rpc;
use Mosaic\Common\Struct;
use Mosaic\SDK\Struct\Product;

use \PHPUnit_Framework_MockObject_Generator as Mocker;

/**
 * Base SDK features context.
 */
class SDKContext extends BehatContext
{
    /**
     * Gateway
     *
     * @var Gateway
     */
    protected $gateway;

    /**
     * Rpc service registry
     *
     * @var Rpc\ServiceRegistry
     */
    protected $service;

    /**
     * Controller
     *
     * @var Controller
     */
    protected $controller;

    public function __construct()
    {
        $this->initGateway();
        $this->initController();

        $this->revisionProvider = new RevisionProvider\Time();
    }

    protected function initGateway()
    {
        $storage = getenv('STORAGE') ?: 'InMemory';
        switch ($storage) {
            case 'InMemory':
                $this->gateway = new Gateway\InMemory();
                return;

            case 'MySQLi':
                $config = @parse_ini_file(__DIR__ . '/../../../build.properties');
                $this->gateway = new Gateway\MySQLi($connection = new MySQLi(
                    $config['db.hostname'],
                    $config['db.userid'],
                    $config['db.password'],
                    $config['db.name']
                ));
                $connection->query('TRUNCATE TABLE mosaic_change;');
                $connection->query('TRUNCATE TABLE mosaic_product;');
                $connection->query('TRUNCATE TABLE mosaic_data;');
                return;

            default:
                throw new \RuntimeException("Unknown storage backend $storage");
        }
    }

    protected function initController()
    {
        $productImporter = \PHPUnit_Framework_MockObject_Generator::getMock('\\Mosaic\\SDK\\ProductImporter');

        $this->service = new Rpc\ServiceRegistry();
        $this->service->registerService(
            'products',
            array('export', 'import', 'getLastRevision'),
            new Service\Product(
                $this->gateway,
                $productImporter
            )
        );

        $this->controller = new Controller(
            $this->service,
            new Rpc\Marshaller\CallUnmarshaller\XmlCallUnmarshaller(),
            new Rpc\Marshaller\CallMarshaller\XmlCallMarshaller(
                new \Mosaic\Common\XmlHelper()
            ),
            new Service\Shopping(
                new ShopFactory()
            )
        );
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
                'shopId' => 'shop-1',
                'sourceId' => (string) $productId,
                'title' => $data,
                'price' => $productId * .89,
                'currency' => 'EUR',
                'availability' => $productId,
            )
        );
    }
}
