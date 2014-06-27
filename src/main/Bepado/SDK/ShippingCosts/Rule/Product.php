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
class Product extends Rule
{
    /**
     * @var string
     */
    public $country;

    /**
     * @var string
     */
    public $region;

    /**
     * @var string
     */
    public $zipRange;

    /**
     * @var int
     */
    public $deliveryWorkDays;

    /**
     * @var string
     */
    public $service;

    /**
     * @var float
     */
    public $price;

    /**
     * @var string
     */
    public $currency;

    /**
     * Check if shipping cost is applicable to given order
     *
     * @param Order $order
     * @return bool
     */
    public function isApplicable(Order $order)
    {
        if (isset($this->country) &&
            ($this->country !== $order->deliveryAddress->country)) {
            return false;
        }

        if (isset($this->zipRange) &&
            !fnmatch($this->zipRange, $order->deliveryAddress->zip)) {
            return false;
        }

        if (isset($this->region) &&
            ($this->region !== $order->deliveryAddress->state)) {
            return false;
        }

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
    public function getShippingCosts(Order $order, OrderItem $orderItem = null)
    {
        return new Shipping(
            array(
                'rule' => $this,
                'service' => $this->service,
                'deliveryWorkDays' => $this->deliveryWorkDays,
                'isShippable' => true,
                'shippingCosts' => $this->price * $orderItem->count,
            )
        );
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
