<?php
/**
 * This file is part of the Bepado SDK Component.
 *
 * The SDK is licensed under MIT license. (c) Shopware AG and Qafoo GmbH
 */

namespace Bepado\SDK;

use Bepado\Common;
use Bepado\SDK\Struct\ShopConfiguration;

class SDKTest extends \PHPUnit_Framework_TestCase
{
    private $sdk;

    private $gatewayMock;

    private $productToShopMock;

    private $productFromShopMock;

    public function setUp()
    {
        $this->gatewayMock = $this->getMock('\\Bepado\\SDK\\Gateway');
        $this->productToShopMock = $this->getMock('\\Bepado\\SDK\\ProductToShop');
        $this->productFromShopMock = $this->getMock('\\Bepado\\SDK\\ProductFromShop');

        $this->sdk = new SDK(
            'apiKey',
            'http://example.com/api',
            $this->gatewayMock,
            $this->productToShopMock,
            $this->productFromShopMock,
            null,
            new HttpClient\NoSecurityRequestSigner()
        );
    }

    public function testGetCategories()
    {
        $this->gatewayMock
            ->expects($this->at(0))
            ->method('getShopId')
            ->will($this->returnValue(1));

        $categories = array(
            '/other' => "Other",
        );

        $this->gatewayMock
            ->expects($this->at(1))
            ->method('getCategories')
            ->will($this->returnValue($categories));

        $this->assertEquals(
            $categories,
            $this->sdk->getCategories()
        );
    }

    public function testGetShop()
    {
        $shopId = 1234;
        $shopConfig = new ShopConfiguration(array(
            'displayName' => 'Test-Shop',
            'url' => 'http://foo',
        ));

        $this->gatewayMock
            ->expects($this->once())
            ->method('getShopConfiguration')
            ->with($this->equalTo($shopId))
            ->will($this->returnValue($shopConfig));

        $shop = $this->sdk->getShop($shopId);

        $this->assertInstanceOf('Bepado\SDK\Struct\Shop', $shop);
        $this->assertEquals('Test-Shop', $shop->name);
        $this->assertEquals($shopId, $shop->id);
        $this->assertEquals('http://foo', $shop->url);
    }

    public function testPingRequest()
    {
        $responseBody = $this->sdk->handle('', array('HTTP_X_BEPADO_PING' => ''));

        $this->assertEquals(
            '<?xml version="1.0" encoding="utf-8"?>'. "\n"
                . '<pong/>',
            $responseBody
        );
    }
}
