<?php

namespace Bepado\SDK\ShippingCosts\Rule;

use Bepado\SDK\Struct;

class UnitPriceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_multiplies_price_by_unit()
    {
        $rule = new UnitPrice(array(
            'price' => 10,
        ));

        $this->assertEquals(50, $rule->getShippingCosts(
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
    }
}
