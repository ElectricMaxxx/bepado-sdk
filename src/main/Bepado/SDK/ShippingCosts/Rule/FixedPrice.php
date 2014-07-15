<?php
/**
 * This file is part of the Bepado Common Component.
 *
 * The SDK is licensed under MIT license. (c) Shopware AG and Qafoo GmbH
 */

namespace Bepado\SDK\ShippingCosts\Rule;

use Bepado\SDK\ShippingCosts\Rule;
use Bepado\SDK\Struct\Order;

/**
 * Class: FixedPrice
 *
 * Rule for fixed price shipping costs for an order
 */
class FixedPrice extends Rule
{
    /**
     * @var string
     */
    public $label;

    /**
     * @var float
     */
    public $price = 0;

    /**
     * Delivery work days
     *
     * @var int
     */
    public $deliveryWorkDays = 10;

    /**
     * Check if shipping cost is applicable to given order
     *
     * @param Order $order
     * @return bool
     */
    public function isApplicable(Order $order)
    {
        return true;
    }

    /**
     * Get shipping costs for order
     *
     * Returns the net shipping costs.
     *
     * @param Order $order
     * @return float
     */
    public function getShippingCosts(Order $order)
    {
        return $this->price;
    }

    /**
     * Get delivery work days for the given order
     *
     * @param Order $order
     * @return int
     */
    public function getDeliveryWorkDays(Order $order)
    {
        return $this->deliveryWorkDays;
    }

    /**
     * If processing should stop after this rule
     *
     * @param Order $order
     * @return bool
     */
    public function shouldStopProcessing(Order $order)
    {
        return true;
    }
}
