<?php

namespace Bepado\SDK\ShippingCosts\Rule;

use Bepado\SDK\Struct;
use Phake;

class MinimumBasketValueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_calculates_free_costs_when_order_total_exceeds_limit()
    {
        $delegatee = Phake::mock('Bepado\SDK\ShippingCosts\Rule');

        $rule = new MinimumBasketValue(array(
            'freeLimit' => 100,
            'delegatee' => $delegatee
        ));

        $this->assertEquals(0, $rule->getShippingCosts(
            new Struct\Order(array(
                'orderItems' => array(
                    new Struct\OrderItem(array(
                        'count' => 2,
                        'product' => new Struct\Product(array(
                            'purchasePrice' => 20,
                            'vat' => 0.19
                        ))
                    )),
                    new Struct\OrderItem(array(
                        'count' => 3,
                        'product' => new Struct\Product(array(
                            'purchasePrice' => 20,
                            'vat' => 0.19
                        ))
                    )),
                )
            ))
        ));

        \Phake::verifyNoInteraction($delegatee);
    }

    /**
     * @test
     */
    public function it_delegates_shipping_costs_when_limit_not_exceeded()
    {
        $delegatee = Phake::mock('Bepado\SDK\ShippingCosts\Rule');
        Phake::when($delegatee)->getShippingCosts(Phake::anyParameters())->thenReturn(10);

        $rule = new MinimumBasketValue(array(
            'freeLimit' => 100,
            'delegatee' => $delegatee
        ));

        $this->assertEquals(10, $rule->getShippingCosts(
            new Struct\Order(array(
                'orderItems' => array(
                    new Struct\OrderItem(array(
                        'count' => 1,
                        'product' => new Struct\Product(array(
                            'purchasePrice' => 10,
                            'vat' => 0.19
                        ))
                    )),
                )
            ))
        ));
    }
}
