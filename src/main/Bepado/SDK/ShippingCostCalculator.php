<?php
/**
 * This file is part of the Bepado SDK Component.
 *
 * The SDK is licensed under MIT license. (c) Shopware AG and Qafoo GmbH
 */

namespace Bepado\SDK;

use Bepado\SDK\ShippingCosts\Rule;

/**
 * Shipping cost calculator
 *
 * The SDK is licensed under MIT license. (c) Shopware AG and Qafoo GmbH
 */
interface ShippingCostCalculator
{
    /**
     * @return \Bepado\SDK\Struct\TotalShippingCosts
     */
    public function calculateShippingCosts(Struct\Order $order);
}
