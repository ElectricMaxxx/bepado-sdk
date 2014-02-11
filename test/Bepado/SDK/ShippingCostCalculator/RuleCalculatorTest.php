<?php

namespace Bepado\SDK\ShippingCostCalculator;

use Bepado\Common\ShippingCosts\Rule;

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
        \Phake::when($this->gateway)->getShippingCosts(1)->thenReturn(
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
