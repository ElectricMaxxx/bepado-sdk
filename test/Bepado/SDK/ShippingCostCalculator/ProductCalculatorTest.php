<?php

namespace Bepado\SDK\ShippingCostCalculator;

use Bepado\SDK\ShippingCosts\Rule;
use Bepado\SDK\ShippingCosts\Rules;
use Bepado\SDK\Struct\Order;
use Bepado\SDK\Struct\ShippingCosts;

class ProductCalculatorTest extends \PHPUnit_Framework_TestCase
{
    private $aggregate;
    private $calculator;

    public function setUp()
    {
        $this->aggregate = \Phake::mock('Bepado\SDK\ShippingCostCalculator');
        $this->calculator = new ProductCalculator($this->aggregate);

        \Phake::when($this->aggregate)->calculateShippingCosts(\Phake::anyParameters())->thenReturn(
            new \Bepado\SDK\Struct\ShippingCosts()
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
                                'shipping' => ':::5.95 EUR',
                            )),
                        )),
                    ),
                )),
                new \Bepado\SDK\Struct\ShippingCosts(array(
                    'isShippable' => true,
                    'shippingCosts' => 5.95,
                    'grossShippingCosts' => 5.95 * 1.19,
                )),
            ),
            array(
                new \Bepado\SDK\Struct\Order(array(
                    'orderItems' => array(
                        new \Bepado\SDK\Struct\OrderItem(array(
                            'count' => 2,
                            'product' => new \Bepado\SDK\Struct\Product(array(
                                'shipping' => ':::5.95 EUR',
                            )),
                        )),
                    ),
                )),
                new \Bepado\SDK\Struct\ShippingCosts(array(
                    'isShippable' => true,
                    'shippingCosts' => 5.95 * 2,
                    'grossShippingCosts' => 5.95 * 1.19 * 2,
                )),
            ),
            array(
                new \Bepado\SDK\Struct\Order(array(
                    'orderItems' => array(
                        new \Bepado\SDK\Struct\OrderItem(array(
                            'count' => 1,
                            'product' => new \Bepado\SDK\Struct\Product(array(
                                'shipping' => ':::5.95 EUR',
                            )),
                        )),
                        new \Bepado\SDK\Struct\OrderItem(array(
                            'count' => 1,
                            'product' => new \Bepado\SDK\Struct\Product(array(
                                'shipping' => ':::7.95 EUR',
                            )),
                        )),
                    ),
                )),
                new \Bepado\SDK\Struct\ShippingCosts(array(
                    'isShippable' => true,
                    'shippingCosts' => 5.95 + 7.95,
                    'grossShippingCosts' => (5.95 + 7.95) * 1.19,
                )),
            ),
        );
    }

    /**
     * @dataProvider getBaskets
     */
    public function testCalculate(Order $order, ShippingCosts $expected)
    {
        $shippingCosts = $this->calculator->calculateShippingCosts($order, 'test');

        $this->assertInstanceOf('Bepado\SDK\Struct\ShippingCosts', $shippingCosts);
        $this->assertEquals($expected, $shippingCosts, "Calculated wrong shipping costs", 0.01);
    }
}
