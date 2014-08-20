<?php

namespace Bepado\SDK\ShippingCostCalculator;

use Bepado\SDK\ShippingCosts\Rule;
use Bepado\SDK\ShippingCosts\Rules;
use Bepado\SDK\Struct;
use Bepado\SDK\Struct\Order;
use Bepado\SDK\Struct\Shipping;
use Bepado\SDK\ShippingRuleParser;

class ProductCalculatorTest extends \PHPUnit_Framework_TestCase
{
    private $aggregate;
    private $gateway;
    private $calculator;

    public function setUp()
    {
        $this->aggregate = \Phake::mock('Bepado\SDK\ShippingCostCalculator');
        $this->calculator = new ProductCalculator(
            $this->aggregate,
            new ShippingRuleParser\Validator(
                new ShippingRuleParser\Google(),
                new Struct\VerificatorDispatcher(
                    array(
                        'Bepado\\SDK\\Struct\\ShippingRules' =>
                            new Struct\Verificator\ShippingRules(),
                        'Bepado\\SDK\\ShippingCosts\\Rule\\Product' =>
                            new Struct\Verificator\ProductRule(),
                    )
                )
            )
        );

        \Phake::when($this->aggregate)->calculateShippingCosts(\Phake::anyParameters())->thenReturn(
            new \Bepado\SDK\Struct\Shipping(array(
                'isShippable' => true,
                'shippingCosts' => .0,
                'grossShippingCosts' => .0,
            ))
        );
    }

    /**
     * Get baskets
     *
     * @return array
     */
    public function getBaskets()
    {
        return array(
            array( // #0
                new \Bepado\SDK\Struct\Order(array(
                    'orderItems' => array(
                        new \Bepado\SDK\Struct\OrderItem(array(
                            'count' => 1,
                            'product' => new \Bepado\SDK\Struct\Product(array(
                                'shipping' => '::Service [3D]:5.00 EUR',
                            )),
                        )),
                    ),
                )),
                new \Bepado\SDK\Struct\Shipping(array(
                    'isShippable' => true,
                    'shippingCosts' => 5.00,
                    'grossShippingCosts' => 5.00 * 1.19,
                    'deliveryWorkDays' => 3,
                    'service' => 'Service',
                )),
                "Calculate simple general shipping rules",
            ),
            array( // #1
                new \Bepado\SDK\Struct\Order(array(
                    'orderItems' => array(
                        new \Bepado\SDK\Struct\OrderItem(array(
                            'count' => 2,
                            'product' => new \Bepado\SDK\Struct\Product(array(
                                'shipping' => '::Service [3D]:5.00 EUR',
                            )),
                        )),
                    ),
                )),
                new \Bepado\SDK\Struct\Shipping(array(
                    'isShippable' => true,
                    'shippingCosts' => 5.00 * 2,
                    'grossShippingCosts' => 5.00 * 1.19 * 2,
                    'deliveryWorkDays' => 3,
                    'service' => 'Service',
                )),
                "Calculate shipping rules for multiple products",
            ),
            array( // #2
                new \Bepado\SDK\Struct\Order(array(
                    'orderItems' => array(
                        new \Bepado\SDK\Struct\OrderItem(array(
                            'count' => 1,
                            'product' => new \Bepado\SDK\Struct\Product(array(
                                'shipping' => '::Service [3D]:5.00 EUR',
                            )),
                        )),
                        new \Bepado\SDK\Struct\OrderItem(array(
                            'count' => 1,
                            'product' => new \Bepado\SDK\Struct\Product(array(
                                'shipping' => '::Service [3D]:7.00 EUR',
                            )),
                        )),
                    ),
                )),
                new \Bepado\SDK\Struct\Shipping(array(
                    'isShippable' => true,
                    'shippingCosts' => 5.00 + 7.00,
                    'grossShippingCosts' => (5.00 + 7.00) * 1.19,
                    'deliveryWorkDays' => 3,
                    'service' => 'Service',
                )),
                "Calculate shipping rules for multiple order items",
            ),
            array( // #3
                new \Bepado\SDK\Struct\Order(array(
                    'orderItems' => array(
                        new \Bepado\SDK\Struct\OrderItem(array(
                            'count' => 1,
                            'product' => new \Bepado\SDK\Struct\Product(array()),
                        )),
                        new \Bepado\SDK\Struct\OrderItem(array(
                            'count' => 1,
                            'product' => new \Bepado\SDK\Struct\Product(array(
                                'shipping' => '::Service [3D]:7.00 EUR',
                            )),
                        )),
                    ),
                )),
                new \Bepado\SDK\Struct\Shipping(array(
                    'isShippable' => true,
                    'shippingCosts' => 7.00,
                    'grossShippingCosts' => 7.00 * 1.19,
                    'deliveryWorkDays' => 3,
                    'service' => 'Service',
                )),
                "Calculate shipping costs for a basket with only partially defined shipping rules",
            ),
            array( // #4
                new \Bepado\SDK\Struct\Order(array(
                    'deliveryAddress' => new \Bepado\SDK\Struct\Address(array(
                        'country' => 'DEU',
                    )),
                    'orderItems' => array(
                        new \Bepado\SDK\Struct\OrderItem(array(
                            'count' => 1,
                            'product' => new \Bepado\SDK\Struct\Product(array(
                                'shipping' => 'GB::Service [3D]:5.00 EUR,DE::Service [3D]:7.00 EUR',
                            )),
                        )),
                    ),
                )),
                new \Bepado\SDK\Struct\Shipping(array(
                    'isShippable' => true,
                    'shippingCosts' => 7.00,
                    'grossShippingCosts' => 7.00 * 1.19,
                    'deliveryWorkDays' => 3,
                    'service' => 'Service',
                )),
                "Calculate shipping costs using the country rule from multiple rules",
            ),
            array( // #5
                new \Bepado\SDK\Struct\Order(array(
                    'deliveryAddress' => new \Bepado\SDK\Struct\Address(array(
                        'country' => 'DEU',
                        'zip' => '45886',
                    )),
                    'orderItems' => array(
                        new \Bepado\SDK\Struct\OrderItem(array(
                            'count' => 1,
                            'product' => new \Bepado\SDK\Struct\Product(array(
                                'shipping' => 'GB::Service [3D]:5.00 EUR,DE:50*:Service [3D]:7.00 EUR,DE::Service [3D]:9.00 EUR',
                            )),
                        )),
                    ),
                )),
                new \Bepado\SDK\Struct\Shipping(array(
                    'isShippable' => true,
                    'shippingCosts' => 9.00,
                    'grossShippingCosts' => 9.00 * 1.19,
                    'deliveryWorkDays' => 3,
                    'service' => 'Service',
                )),
                "Calculate shipping costs using a non matching region wildcard rule from multiple rules",
            ),
            array( // #6
                new \Bepado\SDK\Struct\Order(array(
                    'deliveryAddress' => new \Bepado\SDK\Struct\Address(array(
                        'country' => 'DEU',
                        'zip' => '45886',
                    )),
                    'orderItems' => array(
                        new \Bepado\SDK\Struct\OrderItem(array(
                            'count' => 1,
                            'product' => new \Bepado\SDK\Struct\Product(array(
                                'shipping' => 'GB::Service [3D]:5.00 EUR,DE:45*:Service [3D]:7.00 EUR,DE::Service [3D]:9.00 EUR',
                            )),
                        )),
                    ),
                )),
                new \Bepado\SDK\Struct\Shipping(array(
                    'isShippable' => true,
                    'shippingCosts' => 7.00,
                    'grossShippingCosts' => 7.00 * 1.19,
                    'deliveryWorkDays' => 3,
                    'service' => 'Service',
                )),
                "Calculate shipping costs using a matching region wildcard rule from multiple rules",
            ),
            array( // #7
                new \Bepado\SDK\Struct\Order(array(
                    'deliveryAddress' => new \Bepado\SDK\Struct\Address(array(
                        'country' => 'DEU',
                        'zip' => '45886',
                    )),
                    'orderItems' => array(
                        new \Bepado\SDK\Struct\OrderItem(array(
                            'count' => 1,
                            'product' => new \Bepado\SDK\Struct\Product(array(
                                'shipping' => 'GB::Service [3D]:5.00 EUR,DE:45886:Service [3D]:7.00 EUR,DE::Service [3D]:9.00 EUR',
                            )),
                        )),
                    ),
                )),
                new \Bepado\SDK\Struct\Shipping(array(
                    'isShippable' => true,
                    'shippingCosts' => 7.00,
                    'grossShippingCosts' => 7.00 * 1.19,
                    'deliveryWorkDays' => 3,
                    'service' => 'Service',
                )),
                "Calculate shipping costs using a concrete matching region rule from multiple rules",
            ),
            array( // #8
                new \Bepado\SDK\Struct\Order(array(
                    'deliveryAddress' => new \Bepado\SDK\Struct\Address(array(
                        'country' => 'DEU',
                        'zip' => '45886',
                    )),
                    'orderItems' => array(
                        new \Bepado\SDK\Struct\OrderItem(array(
                            'count' => 1,
                            'product' => new \Bepado\SDK\Struct\Product(array(
                                'shipping' => 'GB::Service [3D]:5.00 EUR',
                            )),
                        )),
                    ),
                )),
                new \Bepado\SDK\Struct\Shipping(array(
                    'isShippable' => false,
                )),
                "Order is not shippable, if no rule matches",
            ),
            array( // #9
                new \Bepado\SDK\Struct\Order(array(
                    'deliveryAddress' => new \Bepado\SDK\Struct\Address(array(
                        'country' => 'DEU',
                        'zip' => '45886',
                    )),
                    'orderItems' => array(
                        new \Bepado\SDK\Struct\OrderItem(array(
                            'count' => 1,
                            'product' => new \Bepado\SDK\Struct\Product(array(
                                'shipping' => 'DE::Service [3D]:5.00 EUR',
                            )),
                        )),
                    ),
                    'orderItems' => array(
                        new \Bepado\SDK\Struct\OrderItem(array(
                            'count' => 1,
                            'product' => new \Bepado\SDK\Struct\Product(array(
                                'shipping' => 'GB::Service [3D]:5.00 EUR',
                            )),
                        )),
                    ),
                )),
                new \Bepado\SDK\Struct\Shipping(array(
                    'isShippable' => false,
                )),
                "One order item is not shippable, if no rule matches",
            ),
            array( // #10
                new \Bepado\SDK\Struct\Order(array(
                    'deliveryAddress' => new \Bepado\SDK\Struct\Address(array(
                        'country' => 'DEU',
                        'state' => 'NRW',
                    )),
                    'orderItems' => array(
                        new \Bepado\SDK\Struct\OrderItem(array(
                            'count' => 1,
                            'product' => new \Bepado\SDK\Struct\Product(array(
                                'shipping' => 'DE:NRW:Service [3D]:5.00 EUR,::Service [3D]:7.00 EUR',
                            )),
                        )),
                    ),
                )),
                new \Bepado\SDK\Struct\Shipping(array(
                    'isShippable' => true,
                    'shippingCosts' => 5.00,
                    'grossShippingCosts' => 5.00 * 1.19,
                    'deliveryWorkDays' => 3,
                    'service' => 'Service',
                )),
                "Calculate shipping costs using a matching region string rule from multiple rules",
            ),
            array( // #11
                new \Bepado\SDK\Struct\Order(array(
                    'deliveryAddress' => new \Bepado\SDK\Struct\Address(array(
                        'country' => 'DEU',
                        'state' => 'RP',
                    )),
                    'orderItems' => array(
                        new \Bepado\SDK\Struct\OrderItem(array(
                            'count' => 1,
                            'product' => new \Bepado\SDK\Struct\Product(array(
                                'shipping' => 'DE:NRW:Service [3D]:5.00 EUR,::Service [3D]:7.00 EUR',
                            )),
                        )),
                    ),
                )),
                new \Bepado\SDK\Struct\Shipping(array(
                    'isShippable' => true,
                    'shippingCosts' => 7.00,
                    'grossShippingCosts' => 7.00 * 1.19,
                    'deliveryWorkDays' => 3,
                    'service' => 'Service',
                )),
                "Calculate shipping costs using a non matching region string rule from multiple rules",
            ),
            array( // #12
                new \Bepado\SDK\Struct\Order(array(
                    'deliveryAddress' => new \Bepado\SDK\Struct\Address(array(
                        'country' => 'DEU',
                        'state' => 'NRW',
                    )),
                    'orderItems' => array(
                        new \Bepado\SDK\Struct\OrderItem(array(
                            'count' => 1,
                            'product' => new \Bepado\SDK\Struct\Product(array(
                                'shipping' => 'DE:NRW:DHL [5D]:5.00 EUR,::Service [3D]:7.00 EUR',
                            )),
                        )),
                    ),
                )),
                new \Bepado\SDK\Struct\Shipping(array(
                    'isShippable' => true,
                    'service' => 'DHL',
                    'deliveryWorkDays' => 5,
                    'shippingCosts' => 5.00,
                    'grossShippingCosts' => 5.00 * 1.19,
                )),
                "Set service name of matched shipping cost rule",
            ),
            array( // #13
                new \Bepado\SDK\Struct\Order(array(
                    'deliveryAddress' => new \Bepado\SDK\Struct\Address(array(
                        'country' => 'DEU',
                        'state' => 'NRW',
                    )),
                    'orderItems' => array(
                        new \Bepado\SDK\Struct\OrderItem(array(
                            'count' => 1,
                            'product' => new \Bepado\SDK\Struct\Product(array(
                                'shipping' => 'DE:NRW:DHL [5D]:5.00 EUR,::Service [3D]:7.00 EUR',
                            )),
                        )),
                        new \Bepado\SDK\Struct\OrderItem(array(
                            'count' => 1,
                            'product' => new \Bepado\SDK\Struct\Product(array(
                                'shipping' => 'DE:NRW:DPD [3D]:5.00 EUR,::Service [3D]:7.00 EUR',
                            )),
                        )),
                    ),
                )),
                new \Bepado\SDK\Struct\Shipping(array(
                    'isShippable' => true,
                    'service' => 'DHL, DPD',
                    'deliveryWorkDays' => 5,
                    'shippingCosts' => 10.00,
                    'grossShippingCosts' => 10.00 * 1.19,
                )),
                "Aggregate service names of matched shipping cost rules",
            ),
            array( // #14
                new \Bepado\SDK\Struct\Order(array(
                    'deliveryAddress' => new \Bepado\SDK\Struct\Address(array(
                        'country' => 'DEU',
                        'state' => 'NRW',
                    )),
                    'orderItems' => array(
                        new \Bepado\SDK\Struct\OrderItem(array(
                            'count' => 1,
                            'product' => new \Bepado\SDK\Struct\Product(array(
                                'shipping' => 'DE:NRW:DHL:5.00 EUR',
                            )),
                        )),
                    ),
                )),
                new \Bepado\SDK\Struct\Shipping(array(
                    'isShippable' => true,
                    'service' => 'DHL',
                    'deliveryWorkDays' => 10,
                    'shippingCosts' => 5.00,
                    'grossShippingCosts' => 5.00 * 1.19,
                )),
                "Use default delivery work days",
            ),
        );
    }

    /**
     * @dataProvider getBaskets
     */
    public function testCalculate(Order $order, Shipping $expected, $message)
    {
        $order->providerShop = 1;
        $order->orderShop = 2;

        $shippingCosts = $this->calculator->calculateShippingCosts(new Rules(), $order);

        $this->assertInstanceOf('Bepado\SDK\Struct\Shipping', $shippingCosts);
        $this->assertEquals($expected, $shippingCosts, "Calculated wrong shipping costs for test: $message", 0.01);
    }
}
