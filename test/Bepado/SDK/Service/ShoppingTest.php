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
            $calc = \Phake::mock('Bepado\SDK\ShippingCostCalculator'),
            \Phake::mock('Bepado\SDK\Gateway\ShopConfiguration')
        );

        \Phake::when($calc)->calculateProductListShippingCosts(\Phake::anyParameters())
            ->thenReturn(new \Bepado\SDK\Struct\ShippingCosts(array('shippingCosts' => 1, 'grossShippingCosts' => 2)))
            ->thenReturn(new \Bepado\SDK\Struct\ShippingCosts(array('shippingCosts' => 4, 'grossShippingCosts' => 8)))
        ;

        $shippingCosts = $shopping->calculateShippingCosts(
            new \Bepado\SDK\Struct\ProductList(array(
                'products' => array(
                    new \Bepado\SDK\Struct\Product(
                        array(
                            'shopId' => 1,
                            'freeDelivery' => false,
                            'vat' => 0.07,
                        )
                    ),
                    new \Bepado\SDK\Struct\Product(
                        array(
                            'shopId' => 2,
                            'freeDelivery' => false,
                            'vat' => 0.19,
                        )
                    ),
                )
            ))
        );

        $this->assertInstanceOf('Bepado\SDK\Struct\ShippingCosts', $shippingCosts);
        $this->assertEquals(5, $shippingCosts->shippingCosts);
        $this->assertEquals(10, $shippingCosts->grossShippingCosts);
    }
}
