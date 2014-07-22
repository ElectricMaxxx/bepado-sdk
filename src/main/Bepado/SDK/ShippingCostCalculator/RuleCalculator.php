<?php
/**
 * This file is part of the Bepado SDK Component.
 *
 * The SDK is licensed under MIT license. (c) Shopware AG and Qafoo GmbH
 */

namespace Bepado\SDK\ShippingCostCalculator;

use Bepado\SDK\Gateway\ShippingCosts;
use Bepado\SDK\ShippingCostCalculator;
use Bepado\SDK\ShippingCosts\Rules;
use Bepado\SDK\Struct;
use Bepado\SDK\Struct\Order;
use Bepado\SDK\Struct\OrderItem;
use Bepado\SDK\Struct\Shipping;

/**
 * Calculate shipping costs based on rules from the gateway.
 */
class RuleCalculator implements ShippingCostCalculator
{
    /**
     * Shipping costs gateway
     *
     * @var Gateway\ShippingCosts
     */
    protected $shippingCosts;

    /**
     * VAT calculator
     *
     * @var RuleCalculator\VatCalculator
     */
    protected $vatCalculator;

    public function __construct(ShippingCosts $shippingCosts, RuleCalculator\VatCalculator $vatCalculator = null)
    {
        $this->shippingCosts = $shippingCosts;
        $this->vatCalculator = $vatCalculator ?: new RuleCalculator\VatCalculator();
    }

    /**
     * Get shipping costs for order
     *
     * @param \Bepado\SDK\Struct\Order $order
     * @param string $type
     *
     * @return \Bepado\SDK\Struct\Order
     */
    public function calculateShippingCosts(Order $order, $type)
    {
        $shippingCostRules = $this->getShippingCostRules($order, $type);
        $this->vatCalculator->calculateVat($order, $shippingCostRules->vatConfig);

        $minShippingCosts = null;
        $minShippingCostValue = PHP_INT_MAX;
        foreach ($shippingCostRules as $shippingCostRule) {
            if ($shippingCostRule->isApplicable($order)) {
                $shippingCosts = $shippingCostRule->getShippingCosts($order, $shippingCostRules->vatConfig);
                if ($shippingCosts->shippingCosts < $minShippingCostValue) {
                    $minShippingCosts = $shippingCosts;
                }
            }
        }

        if (!$minShippingCosts) {
            return new Shipping(
                array(
                    'isShippable' => false,
                )
            );
        }

        return $minShippingCosts;
    }

    /**
     * Get shipping cost rules for current order
     *
     * @param \Bepado\SDK\Struct\Order $order
     * @return Rule[]
     */
    protected function getShippingCostRules(Order $order, $type)
    {
        if (empty($order->providerShop) || empty($order->orderShop)) {
            throw new \InvalidArgumentException(
                "Order#providerShop and Order#orderShop must be non-empty ".
                "to calculate the shipping costs."
            );
        }

        foreach ($order->products as $orderItem) {
            if ($orderItem->product->shopId != $order->providerShop) {
                throw new \InvalidArgumentException(
                    "ShippingCostCalculator can only calculate shipping costs for " .
                    "products belonging to exactly one remote shop."
                );
            }
        }

        $rules = $this->shippingCosts->getShippingCosts($order->providerShop, $order->orderShop, $type);
        if (is_array($rules)) {
            // This is for legacy shops, where the rules are still just an array
            $rules = new Rules(array('rules' => $rules));
        }

        return $rules;
    }
}
