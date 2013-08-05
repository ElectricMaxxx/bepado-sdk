<?php
/**
 * This file is part of the Bepado SDK Component.
 *
 * @version $Revision$
 */

namespace Bepado\SDK;

use Bepado\Common;
use Bepado\SDK\Struct\ShopConfiguration;

require_once __DIR__ . '/bootstrap.php';

class SearchTest extends Common\Test\TestCase
{
    public function testGetCategories()
    {
        $sdk = new SDK(
            'apiKey',
            'http://example.com/api',
            $gatewayMock = $this->getMock('\\Bepado\\SDK\\Gateway'),
            $this->getMock('\\Bepado\\SDK\\ProductToShop'),
            $this->getMock('\\Bepado\\SDK\\ProductFromShop')
        );

        $gatewayMock
            ->expects($this->at(0))
            ->method('getShopId')
            ->will($this->returnValue(1));

        $gatewayMock
            ->expects($this->at(1))
            ->method('getLastVerificationDate')
            ->will($this->returnValue(time()));

        $categories = array(
            '/other' => "Other",
        );

        $gatewayMock
            ->expects($this->at(2))
            ->method('getCategories')
            ->will($this->returnValue($categories));

        $this->assertEquals(
            $categories,
            $sdk->getCategories()
        );
    }

    public function testGetShop()
    {
        $shopId = 1234;
        $shopConfig = new ShopConfiguration(array('displayName' => 'Test-Shop'));

        $sdk = new SDK(
            'apiKey',
            'http://example.com/api',
            $gatewayMock = $this->getMock('\\Bepado\\SDK\\Gateway'),
            $this->getMock('\\Bepado\\SDK\\ProductToShop'),
            $this->getMock('\\Bepado\\SDK\\ProductFromShop')
        );

        $gatewayMock
            ->expects($this->once())
            ->method('getShopConfiguration')
            ->with($this->equalTo($shopId))
            ->will($this->returnValue($shopConfig));

        $shop = $sdk->getShop($shopId);

        $this->assertInstanceOf('Bepado\SDK\Struct\Shop', $shop);
        $this->assertEquals('Test-Shop', $shop->name);
        $this->assertEquals($shopId, $shop->id);
    }
}
