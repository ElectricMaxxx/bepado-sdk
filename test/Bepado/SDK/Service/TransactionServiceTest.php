<?php

namespace Bepado\SDK\Service;

use Bepado\SDK\Struct;

class TransactionServiceTest extends \PHPUnit_Framework_TestCase
{
    static public function matchingAvailabilityGroups()
    {
        return array(
            array(1, 1), // equal
            array(0, 0), // empty
            array(5, 1), // low group
            array(99, 11), // medium group
            array(201, 101), // high group
            array(1, 1000), // we have MUCH MORE suddenly
        );
    }

    public function setUp()
    {
        $this->fromShop = $this->getMock('Bepado\SDK\ProductFromShop');
        $this->gateway = $this->getMock('Bepado\SDK\Gateway\ReservationGateway');
        $this->logger = $this->getMock('Bepado\SDK\Logger', array('doLog', 'confirm'));
        $this->configuration = $this->getMock('Bepado\SDK\Gateway\ShopConfiguration');
        $this->transaction = new Transaction($this->fromShop, $this->gateway, $this->logger, $this->configuration);
    }

    /**
     * @dataProvider matchingAvailabilityGroups
     */
    public function testMatchingAvailabilityGroups($remoteAvailability, $actualAvailability)
    {
        $remoteProduct = new Struct\Product(array(
            'availability' => $remoteAvailability,
            'purchasePrice' => 0,
            'priceGroupMargin' => 0,
        ));
        $localProduct = new Struct\Product(array(
            'availability' => $actualAvailability,
            'purchasePrice' => 0,
            'priceGroupMargin' => 0,
        ));

        $products = new Struct\ProductList(array(
            'products' => array($remoteProduct)
        ));

        $this->fromShop->expects($this->once())->method('getProducts')->will($this->returnValue(array($localProduct)));

        $result = $this->transaction->checkProducts($products);

        $this->assertTrue($result);
    }

    public function testNonMatchingAvailabilityGroups()
    {
        $remoteProduct = new Struct\Product(array(
            'availability' => 100,
        ));
        $localProduct = new Struct\Product(array(
            'availability' => 5,
        ));

        $products = new Struct\ProductList(array(
            'products' => array($remoteProduct)
        ));

        $this->fromShop->expects($this->once())->method('getProducts')->will($this->returnValue(array($localProduct)));

        $result = $this->transaction->checkProducts($products);

        $this->assertContainsOnly('Bepado\SDK\Struct\Change\InterShop\Update', $result);
    }

    public function testCheckIncludesPriceGroupMarginOnPurchasePrice()
    {
        $remoteProduct = new Struct\Product(array(
            'availability' => 100,
            'purchasePrice' => 90,
            'priceGroupMargin' => 10
        ));
        $localProduct = new Struct\Product(array(
            'availability' => 100,
            'purchasePrice' => 100,
            'priceGroupMargin' => 0
        ));

        $products = new Struct\ProductList(array(
            'products' => array($remoteProduct)
        ));

        $this->fromShop->expects($this->once())->method('getProducts')->will($this->returnValue(array($localProduct)));

        $result = $this->transaction->checkProducts($products);

        $this->assertTrue($result);
    }
}

