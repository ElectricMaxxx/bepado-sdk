<?php

namespace Bepado\SDK\ShippingCosts\Rule;

use Bepado\SDK\ShippingCosts\Rule;
use Bepado\SDK\Struct\Order;

class ProductPrice extends Rule
{
    public function isApplicable(Order $order)
    {

    }

    public function getShippingCosts(Order $order)
    {
        return $this->price;
    }

    public function shouldStopProcessing(Order $order)
    {
        return false;
    }
}
