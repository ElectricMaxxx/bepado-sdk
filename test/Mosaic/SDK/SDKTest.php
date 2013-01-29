<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK;

use Mosaic\Common;

require_once __DIR__ . '/bootstrap.php';

class SearchTest extends Common\Test\TestCase
{
    public function testGetCategories()
    {
        $sdk = new SDK(
            'apiKey',
            'http://example.com/api',
            $gatewayMock = $this->getMock('\\Mosaic\\SDK\\Gateway'),
            $this->getMock('\\Mosaic\\SDK\\ProductToShop'),
            $this->getMock('\\Mosaic\\SDK\\ProductFromShop')
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
}
