<?php
/**
 * This file is part of the Bepado SDK Component.
 *
 * @version $Revision$
 */

namespace Bepado\SDK\ShippingCostCalculator\Rule;

use Bepado\SDK\ShippingCostCalculator\Rule;
use Bepado\SDK\Struct;

/**
 * Class: FixedPrice
 *
 * Rule for fixed price shipping costs for an order
 */
class FixedPrice extends Rule
{
    protected $price = 0;

    public function __construct($price)
    {
        $this->price = $price;
    }

    /**
     * Check if shipping cost is applicable to given order
     *
     * @param Struct\Order $order
     * @return bool
     */
    public function isApplicable(Struct\Order $order)
    {
        return true;
    }

    /**
     * Get shipping costs for order
     *
     * Returns the net shipping costs.
     *
     * @param Struct\Order $order
     * @return float
     */
    public function getShippingCosts(Struct\Order $order)
    {
        return $this->price;
    }

    /**
     * If processing should stop after this rule
     *
     * @param Struct\Order $order
     * @return bool
     */
    public function shouldStopProcessing(Struct\Order $order)
    {
        return true;
    }
}
