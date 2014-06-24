<?php
/**
 * This file is part of the Bepado Common Component.
 *
 * The SDK is licensed under MIT license. (c) Shopware AG and Qafoo GmbH
 */

namespace Bepado\SDK\ShippingCosts\Rule;

use Bepado\SDK\ShippingCosts\Rule;
use Bepado\SDK\Struct\Order;
use Bepado\SDK\Struct\OrderItem;
use Bepado\SDK\ShippingRuleParser;

/**
 * Class: FixedPrice
 *
 * Rule for fixed price shipping costs for an order
 */
class Product extends Rule
{
    /**
     * Parser
     *
     * @var ShippingRuleParser
     */
    private $parser;

    /**
     * __construct
     *
     * @param ShippingRuleParser $parser
     * @return void
     */
    public function __construct(ShippingRuleParser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * Check if shipping cost is applicable to given order
     *
     * @param Order $order
     * @param OrderItem $orderItem
     * @return bool
     */
    public function isApplicable(Order $order, OrderItem $orderItem)
    {
        return (bool) $orderItem->product->shipping;
    }

    /**
     * Get shipping costs for order
     *
     * Returns the net shipping costs.
     *
     * @param Order $order
     * @param OrderItem $orderItem
     * @return float
     */
    public function getShippingCosts(Order $order, OrderItem $orderItem)
    {
        $rules = $this->parser->parseString($orderItem->product->shipping);

        foreach ($rules as $rule) {
            if ($rule->isApplicable($order)) {
                $orderItem->shippingCosts += $rule->getShippingCosts($order);

                if ($rule->shouldStopProcessing($order)) {
                    break;
                }
            }
        }
    }

    /**
     * If processing should stop after this rule
     *
     * @param Order $order
     * @param OrderItem $orderItem
     * @return bool
     */
    public function shouldStopProcessing(Order $order, OrderItem $orderItem)
    {
        return (bool) $orderItem->product->shipping;
    }
}
