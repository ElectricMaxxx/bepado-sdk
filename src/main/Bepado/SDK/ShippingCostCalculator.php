<?php
/**
 * This file is part of the Bepado SDK Component.
 *
 * @version $Revision$
 */

namespace Bepado\SDK;

use Bepado\SDK\ShippingCosts\Rule;

/**
 * Shipping cost calculator
 *
 * @version $Revision$
 */
interface ShippingCostCalculator
{
    /**
     * @return \Bepado\SDK\Struct\TotalShippingCosts
     */
    public function calculateShippingCosts(Struct\Order $order);
}
