<?php

namespace Bepado\SDK\ShippingCosts\Rule;

use Bepado\SDK\ShippingCosts\RuleTest;
use Bepado\SDK\ShippingCosts\VatConfig;
use Bepado\SDK\Struct\Shipping;

require_once __DIR__ . '/../RuleTest.php';

class FixedPriceTest extends RuleTest
{
    public function testGetAndSetState()
    {
        $state = array(
            'price' => 5.0,
        );

        $rule = FixedPrice::__set_state($state);

        $this->assertEquals($state['price'], $rule->price);
    }

    public function testIsApplicable()
    {
        $rule = new FixedPrice();

        $this->assertTrue(
            $rule->isApplicable($this->getValidOrder())
        );
    }

    public function testCalculatePrice()
    {
        $rule = FixedPrice::__set_state(
            array(
                'price' => 5.0,
            )
        );

        $this->assertEquals(
            new Shipping(array(
                'rule' => $rule,
                'shippingCosts' => 5.,
                'grossShippingCosts' => 5.5,
                'deliveryWorkDays' => 10,
            )),
            $rule->getShippingCosts(
                $this->getValidOrder(),
                new VatConfig(array(
                    'vat' => .1,
                    'isNet' => true,
                ))
            ),
            'Wrong shipping costs',
            .01
        );
    }

    public function testCalculateGrossPrice()
    {
        $rule = FixedPrice::__set_state(
            array(
                'price' => 5.0,
            )
        );

        $this->assertEquals(
            new Shipping(array(
                'rule' => $rule,
                'shippingCosts' => 4.54,
                'grossShippingCosts' => 5.,
                'deliveryWorkDays' => 10,
            )),
            $rule->getShippingCosts(
                $this->getValidOrder(),
                new VatConfig(array(
                    'vat' => .1,
                    'isNet' => false,
                ))
            ),
            'Wrong shipping costs',
            .01
        );
    }
}
