<?php

namespace Bepado\SDK\ShippingCosts;

use Bepado\SDK\Struct\Order;

abstract class RuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Get valid order
     *
     * @return Order
     */
    protected function getValidOrder()
    {
        return new Order(
            array(
                // @TODO: Fill order with values, as required
            )
        );
    }
}
