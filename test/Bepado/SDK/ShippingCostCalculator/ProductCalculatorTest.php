<?php

namespace Bepado\SDK\ShippingCostCalculator;

use Bepado\SDK\ShippingCosts\Rule;
use Bepado\SDK\ShippingCosts\Rules;

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

    public function testCalculateSingleProductRule()
    {
        $result = $this->calculator->calculateShippingCosts(
            new \Bepado\SDK\Struct\Order(
                array(
                    'orderItems' => array(
                        new \Bepado\SDK\Struct\OrderItem(
                            array(
                                'count' => 1,
                                'product' => new \Bepado\SDK\Struct\Product(
                                    array(
                                        'shipping' => ':::5.95 EUR',
                                    )
                                ),
                            )
                        ),
                    ),
                )
            ),
            'test'
        );

        $this->assertInstanceOf('Bepado\SDK\Struct\ShippingCosts', $result);
        $this->assertEquals(5.95, $result->shippingCosts);
    }
}
