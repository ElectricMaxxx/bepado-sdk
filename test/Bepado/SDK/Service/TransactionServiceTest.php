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

    /**
     * @dataProvider matchingAvailabilityGroups
     */
    public function testMatchingAvailabilityGroups($remoteAvailability, $actualAvailability)
    {
        $fromShop = $this->getMock('Bepado\SDK\ProductFromShop');
        $gateway = $this->getMock('Bepado\SDK\Gateway\ReservationGateway');
        $logger = $this->getMock('Bepado\SDK\Logger', array('doLog', 'confirm'));
        $configuration = $this->getMock('Bepado\SDK\Gateway\ShopConfiguration');

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

        $fromShop->expects($this->once())->method('getProducts')->will($this->returnValue(array($localProduct)));

        $transaction = new Transaction($fromShop, $gateway, $logger, $configuration);
        $result = $transaction->checkProducts($products);

        $this->assertTrue($result);
    }

    public function testNonMatchingAvailabilityGroups()
    {
        $fromShop = $this->getMock('Bepado\SDK\ProductFromShop');
        $gateway = $this->getMock('Bepado\SDK\Gateway\ReservationGateway');
        $logger = $this->getMock('Bepado\SDK\Logger', array('doLog', 'confirm'));
        $configuration = $this->getMock('Bepado\SDK\Gateway\ShopConfiguration');

        $remoteProduct = new Struct\Product(array(
            'availability' => 100,
        ));
        $localProduct = new Struct\Product(array(
            'availability' => 5,
        ));

        $products = new Struct\ProductList(array(
            'products' => array($remoteProduct)
        ));

        $fromShop->expects($this->once())->method('getProducts')->will($this->returnValue(array($localProduct)));

        $transaction = new Transaction($fromShop, $gateway, $logger, $configuration);
        $result = $transaction->checkProducts($products);

        $this->assertContainsOnly('Bepado\SDK\Struct\Change\InterShop\Update', $result);
    }
}

