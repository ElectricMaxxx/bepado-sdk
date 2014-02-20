<?php

namespace Bepado\SDK\ShippingCostCalculator;

use Bepado\SDK\Struct;

class GlobalConfigCalculatorTest extends \PHPUnit_Framework_TestCase
{
    private $gateway;
    private $calculator;

    public function setUp()
    {
        $this->gateway = \Phake::mock('Bepado\SDK\Gateway\ShopConfiguration');
        $this->calculator = new GlobalConfigCalculator($this->gateway);
    }

    public function testCalculateWithMixedVatProductsUsesMaxVat()
    {
        \Phake::when($this->gateway)->getShopConfiguration(1)->thenReturn(
            new Struct\ShopConfiguration(
                array(
                    'shippingCost' => 10,
                )
            )
        );

        $result = $this->calculator->calculateShippingCosts(
            new Struct\Order(
                array(
                    'products' => array(
                        new Struct\OrderItem(array(
                            'product' => new Struct\Product(
                                array(
                                    'shopId' => 1,
                                    'freeDelivery' => false,
                                    'vat' => 0.07,
                                )
                            ),
                        )),
                        new Struct\OrderItem(array(
                            'product' => new Struct\Product(
                                array(
                                    'shopId' => 1,
                                    'freeDelivery' => false,
                                    'vat' => 0.19,
                                )
                            ),
                        )),
                    )
                )
            )
        );

        $this->assertInstanceOf('Bepado\SDK\Struct\ShippingCosts', $result);
        $this->assertTrue($result->isShippable);
        $this->assertEquals(10, $result->shippingCosts);
        $this->assertEquals(11.9, $result->grossShippingCosts);
    }

    public function testCalculationAbortedWhenProductsFromMultipleShops()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'ShippingCostCalculator can only calculate shipping costs for products belonging to exactly one remote shop.'
        );

        $this->calculator->calculateShippingCosts(
            new Struct\Order(
                array(
                    'products' => array(
                        new Struct\OrderItem(array(
                            'product' => new Struct\Product(
                                array(
                                    'shopId' => 1,
                                    'freeDelivery' => false,
                                    'vat' => 0.07,
                                )
                            ),
                        )),
                        new Struct\OrderItem(array(
                            'product' => new Struct\Product(
                                array(
                                    'shopId' => 2,
                                    'freeDelivery' => false,
                                    'vat' => 0.19,
                                )
                            ),
                        )),
                    )
                )
            )
        );
    }
}
