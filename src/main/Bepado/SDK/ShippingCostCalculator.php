<?php
/**
 * This file is part of the Bepado SDK Component.
 *
 * @version $Revision$
 */

namespace Bepado\SDK;

use Bepado\Common\ShippingCosts\Rule;

/**
 * Shipping cost calculator
 *
 * @version $Revision$
 */
interface ShippingCostCalculator
{
    public function calculateShippingCosts(Struct\Order $order);
}
