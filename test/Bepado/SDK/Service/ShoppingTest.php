<?php

namespace Bepado\SDK\Service;

class ShoppingTest extends \PHPUnit_Framework_TestCase
{
    public function testShippingCostSum()
    {
        $shopping = new Shopping(
            \Phake::mock('Bepado\SDK\ShopFactory'),
            \Phake::mock('Bepado\SDK\ChangeVisitor'),
            \Phake::mock('Bepado\SDK\ProductToShop'),
            \Phake::mock('Bepado\SDK\Logger'),
            \Phake::mock('Bepado\SDK\ErrorHandler'),
            $calc = \Phake::mock('Bepado\SDK\Service\ShippingCosts'),
            \Phake::mock('Bepado\SDK\Gateway\ShopConfiguration')
        );

        \Phake::when($calc)->calculateShippingCosts(\Phake::anyParameters())
            ->thenReturn(new \Bepado\SDK\Struct\Shipping(array('shippingCosts' => 1, 'grossShippingCosts' => 2)))
            ->thenReturn(new \Bepado\SDK\Struct\Shipping(array('shippingCosts' => 4, 'grossShippingCosts' => 8)))
        ;

        $result = $shopping->calculateShippingCosts(
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
            ),
            'test'
        );

        $this->assertInstanceOf('Bepado\SDK\Struct\TotalShippingCosts', $result);
        $this->assertEquals(5, $result->shippingCosts);
        $this->assertEquals(10, $result->grossShippingCosts);
    }
}
