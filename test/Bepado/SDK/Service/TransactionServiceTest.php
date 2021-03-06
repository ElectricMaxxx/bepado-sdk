<?php

namespace Bepado\SDK\Service;

use Phake;
use Bepado\SDK\Struct;

class TransactionServiceTest extends \PHPUnit_Framework_TestCase
{
    const BUYER_SHOP_ID = 1;

    static public function matchingAvailabilityGroups()
    {
        return array(
            array(1, 1), // equal
            array(5, 1), // low group
            array(99, 11), // medium group
            array(201, 101), // high group
            array(1, 1000), // we have MUCH MORE suddenly
        );
    }

    public function setUp()
    {
        $this->fromShop = Phake::mock('Bepado\SDK\ProductFromShop');
        $this->gateway = Phake::mock('Bepado\SDK\Gateway\ReservationGateway');
        $this->logger = Phake::mock('Bepado\SDK\Logger');
        $this->configuration = Phake::mock('Bepado\SDK\Gateway\ShopConfiguration');
        $this->calculator = Phake::mock('Bepado\SDK\Service\ShippingCosts');
        $this->transaction = new Transaction(
            $this->fromShop,
            $this->gateway,
            $this->logger,
            $this->configuration,
            $this->calculator,
            \Phake::mock('Bepado\SDK\Struct\VerificatorDispatcher')
        );

        $this->givenPriceGroupMarginIs(0);
    }

    /**
     * @dataProvider matchingAvailabilityGroups
     */
    public function testMatchingAvailabilityGroups($remoteAvailability, $actualAvailability)
    {
        $remoteProduct = new Struct\Product(array(
            'sourceId' => 10,
            'availability' => $remoteAvailability,
            'purchasePrice' => 0,
        ));
        $localProduct = new Struct\Product(array(
            'sourceId' => 10,
            'availability' => $actualAvailability,
            'purchasePrice' => 0,
        ));

        $result = $this->whenCheckProductsWith($localProduct, $remoteProduct);

        $this->assertTrue($result);
    }

    public function testNonMatchingAvailabilityGroups()
    {
        $remoteProduct = new Struct\Product(array(
            'sourceId' => 10,
            'availability' => 100,
        ));
        $localProduct = new Struct\Product(array(
            'sourceId' => 10,
            'availability' => 0,
        ));

        $result = $this->whenCheckProductsWith($localProduct, $remoteProduct);

        $this->assertContainsOnly('Bepado\SDK\Struct\Change\InterShop\Update', $result);
    }

    public function testNegativeAvailabillity()
    {
        $remoteProduct = new Struct\Product(array(
            'sourceId' => 10,
            'availability' => 1,
        ));
        $localProduct = new Struct\Product(array(
            'sourceId' => 10,
            'availability' => -1,
        ));

        $result = $this->whenCheckProductsWith($localProduct, $remoteProduct);

        $this->assertContainsOnly('Bepado\SDK\Struct\Change\InterShop\Update', $result);
    }

    public function testCheckIncludesPriceGroupMarginOnPurchasePrice()
    {
        $this->givenPriceGroupMarginIs(10);

        $remoteProduct = new Struct\Product(array(
            'sourceId' => 10,
            'availability' => 100,
            'purchasePrice' => 90,
        ));
        $localProduct = new Struct\Product(array(
            'sourceId' => 10,
            'availability' => 100,
            'purchasePrice' => 100,
        ));

        $result = $this->whenCheckProductsWith($localProduct, $remoteProduct);

        $this->assertTrue($result);
    }

    public function testIntershopUpdateIncludesPriceGroupMargin()
    {
        $this->givenPriceGroupMarginIs(10);

        $remoteProduct = new Struct\Product(array(
            'sourceId' => 10,
            'availability' => 100,
            'purchasePrice' => 50,
        ));
        $localProduct = new Struct\Product(array(
            'sourceId' => 10,
            'availability' => 100,
            'purchasePrice' => 100,
        ));

        $result = $this->whenCheckProductsWith($localProduct, $remoteProduct);

        $this->assertInstanceOf('Bepado\SDK\Struct\Change\InterShop\Update', $result[0]);
        $this->assertEquals(50, $result[0]->oldProduct->purchasePrice);
        $this->assertEquals(90, $result[0]->product->purchasePrice);
    }

    private function givenPriceGroupMarginIs($margin)
    {
        \Phake::when($this->configuration)
            ->getShopConfiguration(self::BUYER_SHOP_ID)
            ->thenReturn(new Struct\ShopConfiguration(array(
                'priceGroupMargin' => $margin,
            )));
    }

    private function whenCheckProductsWith($localProduct, $remoteProduct)
    {
        $products = new Struct\ProductList(array(
            'products' => array($remoteProduct)
        ));

        \Phake::when($this->fromShop)->getProducts(array(10))->thenReturn(array($localProduct));

        return $this->transaction->checkProducts($products, self::BUYER_SHOP_ID);
    }
}

