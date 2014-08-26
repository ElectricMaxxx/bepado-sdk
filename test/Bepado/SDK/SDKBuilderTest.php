<?php

namespace Bepado\SDK;

use PDO;

class SDKBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildSdkWithAllParameters()
    {
        if (!extension_loaded('pdo') || !extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('Test requires PDO and PDO_SQLITE.');
        }

        $builder = new \Bepado\SDK\SDKBuilder();
        $builder
            ->setApiKey('foo')
            ->setApiEndpointUrl('http://foo/bar')
            ->configurePDOGateway(new PDO('sqlite::memory:'))
            ->setProductToShop(\Phake::mock('Bepado\SDK\ProductToShop'))
            ->setProductFromShop(\Phake::mock('Bepado\SDK\ProductFromShop'))
            ->setErrorHandler(\Phake::mock('Bepado\SDK\ErrorHandler'))
            ->setPluginSoftwareVersion('Foo')
            ->setProductPayments(\Phake::mock('Bepado\SDK\ProductPayments'))
        ;

        $sdk = $builder->build();

        $this->assertInstanceOf('Bepado\SDK\SDK', $sdk);
    }

    public function testBuildSdkWithRequiredOnly()
    {
        if (!extension_loaded('pdo') || !extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('Test requires PDO and PDO_SQLITE.');
        }

        $builder = new \Bepado\SDK\SDKBuilder();
        $builder
            ->setApiKey('foo')
            ->setApiEndpointUrl('http://foo/bar')
            ->configurePDOGateway(new PDO('sqlite::memory:'))
            ->setProductToShop(\Phake::mock('Bepado\SDK\ProductToShop'))
            ->setProductFromShop(\Phake::mock('Bepado\SDK\ProductFromShop'))
            ->setProductPayments(\Phake::mock('Bepado\SDK\ProductPayments'))
        ;

        $sdk = $builder->build();

        $this->assertInstanceOf('Bepado\SDK\SDK', $sdk);
    }

    public function testBuildSdkMissingArgumentsThrowsException()
    {
        $builder = new \Bepado\SDK\SDKBuilder();

        $this->setExpectedException('RuntimeException');
        $builder->build();
    }
}
