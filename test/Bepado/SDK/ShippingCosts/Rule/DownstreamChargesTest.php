<?php

namespace Bepado\SDK\ShippingCosts\Rule;

use Bepado\SDK\Struct\Order;

class DownstreamChargesTest extends \PHPUnit_Framework_TestCase
{
    public function testShippingCostsAlwaysZero()
    {
        $rule = new DownstreamCharges();

        $this->assertEquals(0, $rule->getShippingCosts(new Order()));
    }
}
