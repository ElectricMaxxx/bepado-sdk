<?php

namespace Bepado\SDK\ShippingCosts\Rule;

use Bepado\SDK\Struct\Order;
use Bepado\SDK\Struct\Shipping;

class DownstreamChargesTest extends \PHPUnit_Framework_TestCase
{
    public function testShippingCostsAlwaysZero()
    {
        $rule = new DownstreamCharges();

        $this->assertEquals(
            new Shipping(array(
                'rule' => $rule,
                'deliveryWorkDays' => 10,
            )),
            $rule->getShippingCosts(new Order())
        );
    }
}
