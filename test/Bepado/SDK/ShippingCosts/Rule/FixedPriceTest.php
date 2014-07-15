<?php

namespace Bepado\SDK\ShippingCosts\Rule;

use Bepado\SDK\ShippingCosts\RuleTest;

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
            5.0,
            $rule->getShippingCosts($this->getValidOrder())
        );
    }
}
