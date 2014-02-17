<?php

namespace Bepado\SDK\ShippingCostCalculator;

use Bepado\SDK\ShippingCosts\Rule;

class RuleCalculatorTest extends \PHPUnit_Framework_TestCase
{
    private $gateway;
    private $calculator;

    public function setUp()
    {
        $this->gateway = \Phake::mock('Bepado\SDK\Gateway\ShippingCosts');
        $this->calculator = new RuleCalculator($this->gateway);
    }

    public function testCalculateWithMixedVatProductsUsesMaxVat()
    {
        \Phake::when($this->gateway)->getShippingCosts(1, 2)->thenReturn(
            array(
                new Rule\FixedPrice(
                    array(
                        'price' => 10,
                    )
                )
            )
        );

        $order = $this->calculator->calculateShippingCosts(
            new \Bepado\SDK\Struct\Order(
                array(
                    'orderShop' => 2,
                    'providerShop' => 1,
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

        $this->assertInstanceOf('Bepado\SDK\Struct\Order', $order);
        $this->assertEquals(10, $order->shippingCosts);
        $this->assertEquals(11.9, $order->grossShippingCosts);
    }

    public function testCalculationAbortedWhenProviderOrderShopAreEmpty()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'Order#providerShop and Order#orderShop must be non-empty to calculate the shipping costs.'
        );

        $this->calculator->calculateShippingCosts(
            new \Bepado\SDK\Struct\Order(
                array()
            )
        );
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
                    'orderShop' => 2,
                    'providerShop' => 1,
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
