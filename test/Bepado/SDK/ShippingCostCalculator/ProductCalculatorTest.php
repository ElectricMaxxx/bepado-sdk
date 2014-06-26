<?php

namespace Bepado\SDK\ShippingCostCalculator;

use Bepado\SDK\ShippingCosts\Rule;
use Bepado\SDK\ShippingCosts\Rules;
use Bepado\SDK\Struct\Order;
use Bepado\SDK\Struct\Shipping;

class ProductCalculatorTest extends \PHPUnit_Framework_TestCase
{
    private $aggregate;
    private $calculator;

    public function setUp()
    {
        $this->aggregate = \Phake::mock('Bepado\SDK\ShippingCostCalculator');
        $this->calculator = new ProductCalculator($this->aggregate);

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
            array(
                new \Bepado\SDK\Struct\Order(array(
                    'orderItems' => array(
                        new \Bepado\SDK\Struct\OrderItem(array(
                            'count' => 1,
                            'product' => new \Bepado\SDK\Struct\Product(array(
                                'shipping' => ':::5.00 EUR',
                            )),
                        )),
                    ),
                )),
                new \Bepado\SDK\Struct\Shipping(array(
                    'isShippable' => true,
                    'shippingCosts' => 5.00,
                    'grossShippingCosts' => 5.00 * 1.19,
                )),
            ),
            array(
                new \Bepado\SDK\Struct\Order(array(
                    'orderItems' => array(
                        new \Bepado\SDK\Struct\OrderItem(array(
                            'count' => 2,
                            'product' => new \Bepado\SDK\Struct\Product(array(
                                'shipping' => ':::5.00 EUR',
                            )),
                        )),
                    ),
                )),
                new \Bepado\SDK\Struct\Shipping(array(
                    'isShippable' => true,
                    'shippingCosts' => 5.00 * 2,
                    'grossShippingCosts' => 5.00 * 1.19 * 2,
                )),
            ),
            array(
                new \Bepado\SDK\Struct\Order(array(
                    'orderItems' => array(
                        new \Bepado\SDK\Struct\OrderItem(array(
                            'count' => 1,
                            'product' => new \Bepado\SDK\Struct\Product(array(
                                'shipping' => ':::5.00 EUR',
                            )),
                        )),
                        new \Bepado\SDK\Struct\OrderItem(array(
                            'count' => 1,
                            'product' => new \Bepado\SDK\Struct\Product(array(
                                'shipping' => ':::7.00 EUR',
                            )),
                        )),
                    ),
                )),
                new \Bepado\SDK\Struct\Shipping(array(
                    'isShippable' => true,
                    'shippingCosts' => 5.00 + 7.00,
                    'grossShippingCosts' => (5.00 + 7.00) * 1.19,
                )),
            ),
            array(
                new \Bepado\SDK\Struct\Order(array(
                    'orderItems' => array(
                        new \Bepado\SDK\Struct\OrderItem(array(
                            'count' => 1,
                            'product' => new \Bepado\SDK\Struct\Product(array()),
                        )),
                        new \Bepado\SDK\Struct\OrderItem(array(
                            'count' => 1,
                            'product' => new \Bepado\SDK\Struct\Product(array(
                                'shipping' => ':::7.00 EUR',
                            )),
                        )),
                    ),
                )),
                new \Bepado\SDK\Struct\Shipping(array(
                    'isShippable' => true,
                    'shippingCosts' => 7.00,
                    'grossShippingCosts' => 7.00 * 1.19,
                )),
            ),
            array(
                new \Bepado\SDK\Struct\Order(array(
                    'deliveryAddress' => new \Bepado\SDK\Struct\Address(array(
                        'country' => 'DE',
                    )),
                    'orderItems' => array(
                        new \Bepado\SDK\Struct\OrderItem(array(
                            'count' => 1,
                            'product' => new \Bepado\SDK\Struct\Product(array(
                                'shipping' => 'US:::5.00 EUR,DE:::7.00 EUR',
                            )),
                        )),
                    ),
                )),
                new \Bepado\SDK\Struct\Shipping(array(
                    'isShippable' => true,
                    'shippingCosts' => 7.00,
                    'grossShippingCosts' => 7.00 * 1.19,
                )),
            ),
            array(
                new \Bepado\SDK\Struct\Order(array(
                    'deliveryAddress' => new \Bepado\SDK\Struct\Address(array(
                        'country' => 'DE',
                        'zip' => '45886',
                    )),
                    'orderItems' => array(
                        new \Bepado\SDK\Struct\OrderItem(array(
                            'count' => 1,
                            'product' => new \Bepado\SDK\Struct\Product(array(
                                'shipping' => 'US:::5.00 EUR,DE:50*::7.00 EUR,DE:::9.00 EUR',
                            )),
                        )),
                    ),
                )),
                new \Bepado\SDK\Struct\Shipping(array(
                    'isShippable' => true,
                    'shippingCosts' => 9.00,
                    'grossShippingCosts' => 9.00 * 1.19,
                )),
            ),
            array(
                new \Bepado\SDK\Struct\Order(array(
                    'deliveryAddress' => new \Bepado\SDK\Struct\Address(array(
                        'country' => 'DE',
                        'zip' => '45886',
                    )),
                    'orderItems' => array(
                        new \Bepado\SDK\Struct\OrderItem(array(
                            'count' => 1,
                            'product' => new \Bepado\SDK\Struct\Product(array(
                                'shipping' => 'US:::5.00 EUR,DE:45*::7.00 EUR,DE:::9.00 EUR',
                            )),
                        )),
                    ),
                )),
                new \Bepado\SDK\Struct\Shipping(array(
                    'isShippable' => true,
                    'shippingCosts' => 7.00,
                    'grossShippingCosts' => 7.00 * 1.19,
                )),
            ),
            array(
                new \Bepado\SDK\Struct\Order(array(
                    'deliveryAddress' => new \Bepado\SDK\Struct\Address(array(
                        'country' => 'DE',
                        'zip' => '45886',
                    )),
                    'orderItems' => array(
                        new \Bepado\SDK\Struct\OrderItem(array(
                            'count' => 1,
                            'product' => new \Bepado\SDK\Struct\Product(array(
                                'shipping' => 'US:::5.00 EUR,DE:45886::7.00 EUR,DE:::9.00 EUR',
                            )),
                        )),
                    ),
                )),
                new \Bepado\SDK\Struct\Shipping(array(
                    'isShippable' => true,
                    'shippingCosts' => 7.00,
                    'grossShippingCosts' => 7.00 * 1.19,
                )),
            ),
            array(
                new \Bepado\SDK\Struct\Order(array(
                    'deliveryAddress' => new \Bepado\SDK\Struct\Address(array(
                        'country' => 'DE',
                        'zip' => '45886',
                    )),
                    'orderItems' => array(
                        new \Bepado\SDK\Struct\OrderItem(array(
                            'count' => 1,
                            'product' => new \Bepado\SDK\Struct\Product(array(
                                'shipping' => 'US:::5.00 EUR',
                            )),
                        )),
                    ),
                )),
                new \Bepado\SDK\Struct\Shipping(array(
                    'isShippable' => false,
                    'shippingCosts' => 0.00,
                    'grossShippingCosts' => 0.00,
                )),
            ),
            array(
                new \Bepado\SDK\Struct\Order(array(
                    'deliveryAddress' => new \Bepado\SDK\Struct\Address(array(
                        'country' => 'DE',
                        'zip' => '45886',
                    )),
                    'orderItems' => array(
                        new \Bepado\SDK\Struct\OrderItem(array(
                            'count' => 1,
                            'product' => new \Bepado\SDK\Struct\Product(array(
                                'shipping' => 'US:::5.00 EUR',
                            )),
                        )),
                    ),
                )),
                new \Bepado\SDK\Struct\Shipping(array(
                    'isShippable' => false,
                    'shippingCosts' => 0.00,
                    'grossShippingCosts' => 0.00,
                )),
            ),
            array(
                new \Bepado\SDK\Struct\Order(array(
                    'deliveryAddress' => new \Bepado\SDK\Struct\Address(array(
                        'country' => 'DE',
                        'state' => 'NRW',
                    )),
                    'orderItems' => array(
                        new \Bepado\SDK\Struct\OrderItem(array(
                            'count' => 1,
                            'product' => new \Bepado\SDK\Struct\Product(array(
                                'shipping' => 'DE:NRW::5.00 EUR,:::7.00 EUR',
                            )),
                        )),
                    ),
                )),
                new \Bepado\SDK\Struct\Shipping(array(
                    'isShippable' => true,
                    'shippingCosts' => 5.00,
                    'grossShippingCosts' => 5.00 * 1.19,
                )),
            ),
            array(
                new \Bepado\SDK\Struct\Order(array(
                    'deliveryAddress' => new \Bepado\SDK\Struct\Address(array(
                        'country' => 'DE',
                        'state' => 'RP',
                    )),
                    'orderItems' => array(
                        new \Bepado\SDK\Struct\OrderItem(array(
                            'count' => 1,
                            'product' => new \Bepado\SDK\Struct\Product(array(
                                'shipping' => 'DE:NRW::5.00 EUR,:::7.00 EUR',
                            )),
                        )),
                    ),
                )),
                new \Bepado\SDK\Struct\Shipping(array(
                    'isShippable' => true,
                    'shippingCosts' => 7.00,
                    'grossShippingCosts' => 7.00 * 1.19,
                )),
            ),
            array(
                new \Bepado\SDK\Struct\Order(array(
                    'deliveryAddress' => new \Bepado\SDK\Struct\Address(array(
                        'country' => 'DE',
                        'state' => 'NRW',
                    )),
                    'orderItems' => array(
                        new \Bepado\SDK\Struct\OrderItem(array(
                            'count' => 1,
                            'product' => new \Bepado\SDK\Struct\Product(array(
                                'shipping' => 'DE:NRW:DHL [5D]:5.00 EUR,:::7.00 EUR',
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
            ),
        );
    }

    /**
     * @dataProvider getBaskets
     */
    public function testCalculate(Order $order, Shipping $expected)
    {
        $shippingCosts = $this->calculator->calculateShippingCosts($order, 'test');

        $this->assertInstanceOf('Bepado\SDK\Struct\Shipping', $shippingCosts);
        $this->assertEquals($expected, $shippingCosts, "Calculated wrong shipping costs", 0.01);
    }
}
