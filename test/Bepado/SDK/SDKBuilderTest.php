<?php

namespace Bepado\SDK;

use PDO;

class SDKBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildSdkWithAllParameters()
    {
        $builder = new \Bepado\SDK\SDKBuilder();
        $builder
            ->setApiKey('foo')
            ->setApiEndpointUrl('http://foo/bar')
            ->configurePDOGateway(\Phake::mock('PDO'))
            ->setProductToShop(\Phake::mock('Bepado\SDK\ProductToShop'))
            ->setProductFromShop(\Phake::mock('Bepado\SDK\ProductFromShop'))
            ->setErrorHandler(\Phake::mock('Bepado\SDK\ErrorHandler'))
            ->setPluginSoftwareVersion('Foo')
        ;

        $sdk = $builder->build();

        $this->assertInstanceOf('Bepado\SDK\SDK', $sdk);
    }

    public function testBuildSdkWithRequiredOnly()
    {
        $builder = new \Bepado\SDK\SDKBuilder();
        $builder
            ->setApiKey('foo')
            ->setApiEndpointUrl('http://foo/bar')
            ->configurePDOGateway(\Phake::mock('PDO'))
            ->setProductToShop(\Phake::mock('Bepado\SDK\ProductToShop'))
            ->setProductFromShop(\Phake::mock('Bepado\SDK\ProductFromShop'))
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
