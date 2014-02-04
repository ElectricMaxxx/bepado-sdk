<?php
/**
 * This file is part of the Bepado SDK Component.
 *
 * @version $Revision$
 */

namespace Bepado\SDK\ShippingCostCalculator;

use Bepado\SDK\Struct;

abstract class ShippingCostRule
{
    /**
     * Check if shipping cost is applicable to given order
     *
     * @param Struct\Order $order
     * @return bool
     */
    abstract public function isApplicable(Struct\Order $order);

    /**
     * Get shipping costs for order
     *
     * @param Struct\Order $order
     * @return Struct\ShippingCosts
     */
    abstract public function getShippingCosts(Struct\Order $order);

    /**
     * If processing should stop after this rule
     *
     * @param Struct\Order $order
     * @return bool
     */
    abstract public function shouldStopProcessing(Struct\Order $order);
}
