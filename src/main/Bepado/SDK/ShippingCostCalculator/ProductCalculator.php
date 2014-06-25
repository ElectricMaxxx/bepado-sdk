<?php
/**
 * This file is part of the Bepado SDK Component.
 *
 * The SDK is licensed under MIT license. (c) Shopware AG and Qafoo GmbH
 */

namespace Bepado\SDK\ShippingCostCalculator;

use Bepado\SDK\ShippingCostCalculator;
use Bepado\SDK\ShippingRuleParser;
use Bepado\SDK\Struct\Order;
use Bepado\SDK\Struct\OrderItem;
use Bepado\SDK\Struct\ShippingRule;
use Bepado\SDK\Struct\ShippingCosts;

/**
 * Calculate shipping costs based on product rules
 */
class ProductCalculator implements ShippingCostCalculator
{
    /**
     * Aggregate for products without specific rules
     *
     * @var ShippingCostCalculator
     */
    private $aggregate;

    public function __construct(ShippingCostCalculator $aggregate, ShippingRuleParser $parser = null, Aggregator $aggregator = null)
    {
        $this->aggregate = $aggregate;
        $this->parser = $parser ?: new ShippingRuleParser\Google();
        $this->aggregator = $aggregator ?: new Aggregator\Sum();
    }

    /**
     * Get shipping costs for order
     *
     * @param \Bepado\SDK\Struct\Order $order
     * @param string $type
     *
     * @return Struct\ShippingCosts
     */
    public function calculateShippingCosts(Order $order, $type)
    {
        $productOrder = clone $order;
        $commonOrder = clone $order;

        $productOrder->orderItems = array_filter(
            $productOrder->orderItems,
            function (OrderItem $orderItem) {
                return (bool) $orderItem->product->shipping;
            }
        );

        $commonOrder->orderItems = array_filter(
            $commonOrder->orderItems,
            function (OrderItem $orderItem) {
                return !$orderItem->product->shipping;
            }
        );

        foreach ($productOrder->orderItems as $orderItem) {
            $rules = $this->parser->parseString($orderItem->product->shipping);

            foreach ($rules->rules as $rule) {
                if ($this->matchRule($rule, $order, $orderItem)) {
                    break;
                }
            }
        }

        $productOrder->shippingCosts = $this->aggregator->aggregateShippingCosts($productOrder);
        $commonOrder->shippingCosts = $this->aggregate->calculateShippingCosts($commonOrder, $type);

        $productOrder->shippingCosts->isShippable =
            $productOrder->shippingCosts->isShippable &&
            $commonOrder->shippingCosts->isShippable;
        $productOrder->shippingCosts->shippingCosts =
            $productOrder->shippingCosts->shippingCosts +
            $commonOrder->shippingCosts->shippingCosts;
        $productOrder->shippingCosts->grossShippingCosts =
            $productOrder->shippingCosts->grossShippingCosts +
            $commonOrder->shippingCosts->grossShippingCosts;

        $order->shippingCosts = $productOrder->shippingCosts;
        return $order->shippingCosts;
    }

    /**
     * Match rule
     *
     * Returns true, if rule processing shuld be stopped
     *
     * @param ShippingRule $rule
     * @param Order $order
     * @param OrderItem $orderItem
     * @return bool
     */
    protected function matchRule(ShippingRule $rule, Order $order, OrderItem $orderItem)
    {
        $orderItem->shippingCosts = $rule->price * $orderItem->count;

        if (isset($rule->country) &&
            ($rule->country !== $order->deliveryAddress->country)) {
            return false;
        }

        return true;
    }
}
