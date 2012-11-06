<?php

namespace Mosaic\SDK;

use Behat\Behat\Context\BehatContext;

use Mosaic\Common\Rpc;
use Mosaic\Common\Struct;

use \PHPUnit_Framework_Assert as Assertion;

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
        $this->initDatabase();
        $this->initController();

        $this->revisionProvider = new RevisionProvider\Time();
    }

    protected function initDatabase()
    {
        $config = @parse_ini_file(__DIR__ . '/../../../build.properties');
        $this->gateway = new Gateway\MySQLi($connection = new MySQLi(
            $config['db.hostname'],
            $config['db.userid'],
            $config['db.password'],
            $config['db.name']
        ));
        $connection->query('TRUNCATE TABLE mosaic_change;');
        $connection->query('TRUNCATE TABLE mosaic_product;');
    }

    protected function initController()
    {
        $this->service = new Rpc\ServiceRegistry();
        $this->service->registerService(
            'products',
            array('export'),
            new Service\Product($this->gateway)
        );

        $this->controller = new Controller(
            $this->service,
            new Rpc\Marshaller\CallUnmarshaller\XmlCallUnmarshaller(),
            new Rpc\Marshaller\CallMarshaller\XmlCallMarshaller(
                new \Mosaic\Common\XmlHelper()
            ),
            new Service\Shopping()
        );
    }
}
