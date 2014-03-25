<?php

namespace Bepado\SDK\ShippingCostCalculator;

use Bepado\SDK\ShippingCosts\Rule;
use Bepado\SDK\ShippingCosts\Rules;

class RuleCalculatorTest extends \PHPUnit_Framework_TestCase
{
    private $gateway;
    private $calculator;

    public function setUp()
    {
        $this->gateway = \Phake::mock('Bepado\SDK\Gateway\ShippingCosts');
        $this->calculator = new RuleCalculator($this->gateway);
    }

    public function testCalculateMixedVatUsesDominantStrategy()
    {
        \Phake::when($this->gateway)->getShippingCosts(1, 2, 'test')->thenReturn(
            new Rules(
                array(
                    'vatMode' => Rules::VAT_DOMINATING,
                    'rules' => array(
                        new Rule\FixedPrice(
                            array(
                                'price' => 10,
                            )
                        )
                    )
                )
            )
        );

        $result = $this->calculator->calculateShippingCosts(
            new \Bepado\SDK\Struct\Order(
                array(
                    'orderShop' => 2,
                    'providerShop' => 1,
                    'products' => array(
                        new \Bepado\SDK\Struct\OrderItem(
                            array(
                                'count' => 20,
                                'product' => new \Bepado\SDK\Struct\Product(
                                    array(
                                        'shopId' => 1,
                                        'freeDelivery' => false,
                                        'vat' => 0.07,
                                        'price' => 5,
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
                                        'price' => 10,
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
        $this->assertEquals(10, $result->shippingCosts);
        $this->assertEquals(10.7, $result->grossShippingCosts);
    }

    public function testCalculateWithMixedVatProductsUsesMaxVat()
    {
        \Phake::when($this->gateway)->getShippingCosts(1, 2, 'test')->thenReturn(
            new Rules(
                array(
                    'vatMode' => Rules::VAT_MAX,
                    'rules' => array(
                        new Rule\FixedPrice(
                            array(
                                'price' => 10,
                            )
                        )
                    )
                )
            )
        );

        $result = $this->calculator->calculateShippingCosts(
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
            ),
            'test'
        );

        $this->assertInstanceOf('Bepado\SDK\Struct\ShippingCosts', $result);
        $this->assertEquals(10, $result->shippingCosts);
        $this->assertEquals(11.9, $result->grossShippingCosts);
    }

    public function testCalculateVatModeFixedVat()
    {
        \Phake::when($this->gateway)->getShippingCosts(1, 2, 'test')->thenReturn(
            new Rules(
                array(
                    'vatMode' => Rules::VAT_FIX,
                    'vat' => 0.07,
                    'rules' => array(
                        new Rule\FixedPrice(
                            array(
                                'price' => 10,
                            )
                        )
                    )
                )
            )
        );

        $result = $this->calculator->calculateShippingCosts(
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

        $this->assertInstanceOf('Bepado\SDK\Struct\ShippingCosts', $result);
        $this->assertEquals(10, $result->shippingCosts);
        $this->assertEquals(10.7, $result->grossShippingCosts);
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
            ),
            'test'
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
            ),
            'test'
        );
    }
}
