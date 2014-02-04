<?php

namespace Bepado\SDK;

class ShippingCostCalculatorTest extends \PHPUnit_Framework_TestCase
{
    private $gateway;
    private $calculator;

    public function setUp()
    {
        $this->gateway = \Phake::mock('Bepado\SDK\Gateway\ShopConfiguration');
        $this->calculator = new ShippingCostCalculator($this->gateway);
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

        $shippingCosts = $this->calculator->calculateShippingCosts(
            new \Bepado\SDK\Struct\Order(
                array(
                    'products' => array(
                        new \Bepado\SDK\Struct\OrderItem(
                            array(
                                'count' => 1,
                                'product' => new \Bepado\SDK\Struct\Product(
                                    array(
                                        'shopId' => 1,
                                        'freeDelivery' => false,
                                        'vat' => 0.07,
                                    )
                                ),
                            )
                        ),
                        new \Bepado\SDK\Struct\OrderItem(
                            array(
                                'count' => 1,
                                'product' => new \Bepado\SDK\Struct\Product(
                                    array(
                                        'shopId' => 1,
                                        'freeDelivery' => false,
                                        'vat' => 0.19,
                                    )
                                ),
                            )
                        ),
                    ),
                )
            )
        );

        $this->assertInstanceOf('Bepado\SDK\Struct\ShippingCosts', $shippingCosts);
        $this->assertEquals(10, $shippingCosts->shippingCosts);
        $this->assertEquals(11.9, $shippingCosts->grossShippingCosts);
    }

    public function testCalculationAbortedWhenProductsFromMultipleShops()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'ShippingCostCalculator can only calculate shipping costs for products belonging to exactly one remote shop.'
        );

        $this->calculator->calculateShippingCosts(
            new \Bepado\SDK\Struct\Order(
                array(
                    'products' => array(
                        new \Bepado\SDK\Struct\OrderItem(
                            array(
                                'count' => 1,
                                'product' => new \Bepado\SDK\Struct\Product(
                                    array(
                                        'shopId' => 1,
                                        'freeDelivery' => false,
                                        'vat' => 0.07,
                                    )
                                ),
                            )
                        ),
                        new \Bepado\SDK\Struct\OrderItem(
                            array(
                                'count' => 1,
                                'product' => new \Bepado\SDK\Struct\Product(
                                    array(
                                        'shopId' => 2,
                                        'freeDelivery' => false,
                                        'vat' => 0.19,
                                    )
                                ),
                            )
                        ),
                    ),
                )
            )
        );
    }
}
